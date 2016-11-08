<?php

namespace common\extensions\nas;

use \Yii;
use \Exception;
use \common\helpers\CacheHelper;

/**
 * 流管理
 */
class StreamFile
{
    private $path;

	private $fh;

	private $dfh;

	private $writed = false;

	public function __construct()
	{
	}

	public function __destruct()
	{
	}
   
	/**
	 * 打开文件
	 * (需控情况判断是否从远程下载数据，如：文件存在且为读取模式时)
	 */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $this->path = $path;

        $url  = parse_url($path);
        $file = $url["path"];

		$this->fh = fopen($file, $mode);

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
		if ($this->writed) {
			$this->clearCache($this->path);
		}

		return fclose($this->fh);
	}

	/**
	 * 创建文件夹
	 */
	public function mkdir($path, $mode, $options)
	{
        $url = parse_url($path);
		$file = $url['path'];

		$re = mkdir($file, $mode, $options);

		if ($re) {
			$this->clearCache($path);
		}

		return $re;
	}

	/**
	 * 删除文件
	 */
	public function unlink($path)
	{
        $url = parse_url($path);
		$file = $url['path'];

		$re = unlink($file);

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
        $url = parse_url($path);
		$file = $url['path'];

		$re = rmdir($file, $options);

		if ($re) {
			$this->clearCache($path);
		}

		return $re;
	}

	public function rename($path_from, $path_to)
	{
        $url = parse_url($path_from);
        $url2 = parse_url($path_to);

		$from_file = $url['path'];
		$to_file = $url2['path'];

		$re = rename($from_file, $to_file);

		if ($re) {
			$this->clearCache($path_from);
			$this->clearCache($path_to);
		}

		return $re;
	}

	public function dir_opendir($path, $options)
	{
        $url = parse_url($path);
		$file = $url['path'];

		$this->dfh = opendir($file);

		return $this->dfh ? true : false;
	}

	public function dir_closedir()
	{
		closedir($this->dfh);
	}

	public function dir_readdir()
	{
		return readdir($this->dfh);
	}

	public function dir_rewinddir()
	{
		return rewinddir($this->dfh);
	}

	public function url_stat($path, $flags)
	{
        $url = parse_url($path);

		$file = $url['path'];

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
