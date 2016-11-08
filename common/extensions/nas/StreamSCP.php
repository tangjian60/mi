<?php

namespace common\extensions\nas;

use \Yii;
use \Exception;
use \common\helpers\CacheHelper;

/**
 * 流管理
 */
class StreamSCP
{
    private $type;
    private $path;

	private $tmpfile;
	private $fh;

	private $writed = false;

	private $_remote_ip; 
	private $_remote_user;
	private $_identity_file;

	private $_initd = false;

	public function _init()
	{
		if ($this->_initd) {
			return;
		}

		$this->_initd = true;

		$config = Yii::app()->getComponent('config');

		$this->_remote_ip = $config->get('scp.host');
		$this->_remote_user = $config->get('scp.user');
		$this->_identity_file = $config->get('scp.id');

		$this->tmpfile = tempnam('/tmp', 'nas_');
	}

	public function __destruct()
	{
		if ($this->tmpfile) {
			unlink($this->tmpfile);
		}
	}
   
	/**
	 * 打开文件
	 * (需控情况判断是否从远程下载数据，如：文件存在且为读取模式时)
	 */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
		$this->_init();

		$this->path = $path;

		if (strpos($mode, 'r') !== FALSE || strpos($mode, 'a') !== FALSE) {
			if (file_exists($path)) {
				$this->down();
			}
		}

		$this->fh = fopen($this->tmpfile, $mode);

		if (!$this->fh) {
			return false;
		}
	
		return true;
    }

    public function stream_read($count)
    {
		return fread($this->fh, $count);
    }

    public function stream_write($data)
    {
		return fwrite($this->fh, $data);
    }

    public function stream_tell()
    {
		return ftell($this->fh);
    }

    public function stream_eof()
    {
		return feof($this->fh);
    }

	public function stream_truncate($new_size)
	{
		return ftruncate($this->fh, $new_size);
	}

    public function stream_seek($offset, $whence)
    {
		return fseek($this->fh, $offset, $whence);
    }

	public function stream_flush()
	{
		return fflush($this->fh);
	}

	/**
	 * 关闭句柄时保存文件
	 * (后期可以改为有个专门进程来处理保存工作，加快页面反应)
	 */
	public function stream_close()
	{
		if ($this->writed) {
			$this->upload();
			$this->clearCache($this->path);
		}

		return fclose($this->fh);
	}

	/**
	 * 创建文件夹
	 */
	public function mkdir($path, $mode, $options)
	{
		$this->_init();

        $url = parse_url($path);
		$file = $url['path'];

		$mode = decoct($mode);

		if (STREAM_MKDIR_RECURSIVE & $options) {
			$cmd = "mkdir --mode={$mode} -p {$file}";
		}
		else {
			$cmd = "mkdir --mode={$mode} {$file}";
		}

		$ssh = "ssh -i {$this->_identity_file} {$this->_remote_user}@{$this->_remote_ip} \"{$cmd}\"";

		exec($ssh, $output, $return_var);

		if ($return_var) {
			return false;
		}

		$this->clearCache($path);

		return true;
	}

	/**
	 * 删除文件
	 */
	public function unlink($path)
	{
		$this->_init();

        $url = parse_url($path);
		$file = $url['path'];

		$cmd = "rm -f {$file}";

		$ssh = "ssh -i {$this->_identity_file} {$this->_remote_user}@{$this->_remote_ip} \"{$cmd}\"";

		exec($ssh, $output, $return_var);

		if ($return_var) {
			return false;
		}

		$this->clearCache($path);

		return true;
	}

	/**
	 * 删除文件夹
	 */
	public function rmdir($path, $options)
	{
		$this->_init();

        $url = parse_url($path);
		$file = $url['path'];

		if (STREAM_MKDIR_RECURSIVE & $options) {
			$cmd = "rm -rf {$file}";
		}
		else {
			$cmd = "rm -f {$file}";
		}

		$ssh = "ssh -i {$this->_identity_file} {$this->_remote_user}@{$this->_remote_ip} \"{$cmd}\"";

		exec($ssh, $output, $return_var);

		if ($return_var) {
			return false;
		}

		$this->clearCache($path);

		return true;
	}

	public function rename($path_from, $path_to)
	{
		$this->_init();

        $url = parse_url($path_from);
        $url2 = parse_url($path_to);

		$from = $url['path'];
		$to   = $url2['path'];

		$cmd = "rename {$from} {$to}";

		$ssh = "ssh -i {$this->_identity_file} {$this->_remote_user}@{$this->_remote_ip} \"{$cmd}\"";

		exec($ssh, $output, $return_var);

		if ($return_var) {
			return false;
		}

		$this->clearCache($path_from);
		$this->clearCache($path_to);

		return true;
	}

	public function url_stat($path, $flags)
	{
		$this->_init();

        $url = parse_url($path);

		$file = $url['path'];

		$cmd = "if [ -a {$file} ]; then exit 0; else exit 1; fi";
		$ssh = "ssh -i {$this->_identity_file} {$this->_remote_user}@{$this->_remote_ip} \"{$cmd}\"";

		exec($ssh, $output, $return_var);

		if ($return_var) {
			if (!(STREAM_URL_STAT_QUIET & $flags)) {
				trigger_error('stat error', E_USER_ERROR);
			}

			return 0;
		}
		//这边因为取不到服务器端的状态,只是返回本地的
		else if (STREAM_URL_STAT_LINK & $flags) {
			return lstat($this->tmpfile);
		}
		else {
			return stat($this->tmpfile);
		}
	}

	/* 待实现
	public dir_closedir () { }
	public dir_opendir ( string $path , int $options ) {}
	public dir_readdir () {}
	public dir_rewinddir () {}
	public function stream_stat ( void )
	*/

	/*不实现的接口
	public function stream_cast ( int $cast_as )
	public function stream_metadata ( string $path , int $option , mixed $value )
	public function stream_lock ( mode $operation )
	public function stream_set_option ( int $option , int $arg1 , int $arg2 )
	*/

	private function down()
	{
        $url = parse_url($this->path);
		$path = $url['path'];

		$file = "{$this->_remote_user}@{$this->_remote_ip}:{$path}";

		$cmd = "scp -i {$this->_identity_file} {$file} {$this->tmpfile}";

		exec($cmd, $output, $return_var);

		if ($return_var) {
			throw new Exception("cmd:{$cmd}");
		}
	}

	private function upload()
	{
        $url = parse_url($this->path);
		$path = $url['path'];

		$file = "{$this->_remote_user}@{$this->_remote_ip}:{$path}";

		$cmd = "scp -i {$this->_identity_file} {$this->tmpfile} {$file}";

		exec($cmd, $output, $return_var);

		if ($return_var) {
			throw new Exception("cmd:{$cmd}");
		}
	}

	private function clearCache($file)
	{
		$upload = Yii::app()->getComponent('upload');

		$url = $upload->fileToUrl($file);

		CacheHelper::flushCDNCache($url);
		CacheHelper::flushSquidCache($url);
	}

}
