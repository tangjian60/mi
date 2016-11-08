<?php

namespace common\extensions\nas;

use \Yii;
use \Exception;
use \common\helpers\CacheHelper;

/**
 * 流管理
 */
class StreamFTP
{
    private $path;

	private $fh;

	private $dfh;

	private $writed = false;

	private $_initd = false;
	private $_ftp;
	private $_host;
	private $_port;
	private $_timeout;
	private $_user;
	private $_pass;

	public function _init($connect=false)
	{
		if ($this->_initd) {
			return;
		}

		$this->_initd = true;

		$config = Yii::app()->getComponent('config');

		$this->_host = $config->get('ftp.host');
		$this->_user = $config->get('ftp.user');
		$this->_pass = $config->get('ftp.pass');
		$this->_port = $config->get('ftp.port', 21);
		$this->_timeout = $config->get('ftp.timeout', 10);

		if ($connect) {
			$this->_ftp = ftp_connect($this->_host, $this->_port, $this->_timeout);

			if (!$this->_ftp) {
				throw new Exception("1链接FTP失败");
			}

			if (!ftp_login($this->_ftp, $this->_user, $this->_pass)) {
				throw new Exception("2登陆FTP失败");
			}
		}
	}

	public function __construct()
	{
	}

	public function __destruct()
	{
		if ($this->_ftp) {
			ftp_close($this->_ftp);
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

		$url  = parse_url($path);
		$file = "ftp://{$this->_user}:{$this->_pass}@{$this->_host}{$url["path"]}";

		$ctx = stream_context_create(array(
			'ftp' => array(
				'overwrite' => true,
			),
		)); 

		$this->fh = fopen($file, $mode, 0, $ctx);

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
		$this->writed = true;
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

	public function stream_stat()
	{
		return fstat($this->fh);
	}

	/**
	 * 关闭句柄时保存文件
	 * (后期可以改为有个专门进程来处理保存工作，加快页面反应)
	 */
	public function stream_close()
	{
		if (!fclose($this->fh)) {
			return false;
		}

		if ($this->writed) {
			$this->clearCache($this->path);
		}
	}

	/**
	 * 创建文件夹
	 */
	public function mkdir($path, $mode, $options)
	{
		$this->_init(true);

		$url  = parse_url($path);

		if ($options) {
			$path2 = dirname($path);
			$tmp = parse_url($path2);

			if (isset($tmp['path'])) {
				if (!file_exists($path2)) {
					mkdir($path2, $mode, $options);
				}
			}
		}

		/*
		$file = "ftp://{$this->_user}:{$this->_pass}@{$this->_host}{$url["path"]}";
		$re = mkdir($file, $mode, $options);
		 */

		ftp_mkdir($this->_ftp, $url['path']);

		//$this->clearCache($path);

		return true;
	}

	/**
	 * 删除文件
	 */
	public function unlink($path)
	{
		$this->_init(true);

		$url  = parse_url($path);

		/*
		$file = "ftp://{$this->_user}:{$this->_pass}@{$this->_host}{$url["path"]}";
		$re = unlink($file);
		 */

		$re = ftp_delete($this->_ftp, $url['path']);

		if ($re) {
			$this->clearCache($path);
		}

		return $re;
	}

	/**
	 * 删除文件夹
	 */
	public function rmdir($path, $options)
	{
		$this->_init(true);

		$url  = parse_url($path);

		/*
		$file = "ftp://{$this->_user}:{$this->_pass}@{$this->_host}{$url["path"]}";
		$re = rmdir($file);
		 */

		$re = ftp_rmdir($this->_ftp, $url['path']);

		if ($re) {
			//$this->clearCache($path);
		}

		return $re;
	}

	public function rename($path_from, $path_to)
	{
		$this->_init();

		$url  = parse_url($path_from);
		$from_file = "ftp://{$this->_user}:{$this->_pass}@{$this->_host}{$url["path"]}";

		$url2  = parse_url($path_to);
		$to_file = "ftp://{$this->_user}:{$this->_pass}@{$this->_host}{$url2["path"]}";

		$re = rename($from_file, $to_file);

		if ($re) {
			$this->clearCache($path_from);
			$this->clearCache($path_to);
		}

		return $re;
	}

	public function url_stat($path, $flags)
	{
		$this->_init();

		$url  = parse_url($path);
		$file = "ftp://{$this->_user}:{$this->_pass}@{$this->_host}{$url["path"]}";

		if (!file_exists($file)) {
			if (!(STREAM_URL_STAT_QUIET & $flags)) {
				trigger_error('stat error', E_USER_ERROR);
			}

			return 0;
		}
		else if (STREAM_URL_STAT_LINK & $flags) {
			return lstat($file);
		}
		else {
			return stat($file);
		}
	}

	/*不实现的接口
	public function stream_cast ( int $cast_as )
	public function stream_metadata ( string $path , int $option , mixed $value )
	public function stream_lock ( mode $operation )
	public function stream_set_option ( int $option , int $arg1 , int $arg2 )
	*/

	private function clearCache($file)
	{
		$upload = Yii::app()->getComponent('upload');

		$url = $upload->fileToUrl($file);

		CacheHelper::flushCDNCache($url);
		CacheHelper::flushSquidCache($url);
	}

}
