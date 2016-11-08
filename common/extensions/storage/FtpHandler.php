<?php
/**
 * ftp
 *
 * @author     $Author: wuzhiqiang $
 * @version    $Rev: 817 $
 * @date       $Date: 2013-09-17 10:00:49 +0800 (星期二, 17 九月 2013) $
 * @copyright  2003-2013 DFWSGROUP.COM
 * @link       http://tc.dfwsgroup.com/
 */

namespace common\extensions\storage;

use common\extensions\storage\HandlerInterface;
use common\extensions\storage\Exception as HandlerException;

class FtpHandler implements HandlerInterface
{

    /**
     * FTP用户名
     * @var string
     */
    private $_username = '';

    /**
     * FTP密码
     * @var string
     */
    private $_password = '';

    /**
     * FTP主机地址
     * @var string
     */
    private $_host = 'localhost';

    public function init(array $params = array())
    {
        foreach($params as $key => $val) {
            $name = '_' . $key;
            if(property_exists($this, $name)) $this->$name = $val;
        }
    }

    public function __set($name, $value)
    {
        $name = '_' . $name;
        if(isset($this->$name)) {
            $this->$name = $value;
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 创建流字符串
     * 
     * @param  string $path [description]
     * @return [type]       [description]
     */
    public function buildStreamString($protocol = 'ftp', $path = '')
    {
        $str = $protocol . '://';

        if(in_array($protocol, array('ftp', 'http', 'ssh2') )) {
            $str .= (empty($this->_username) ?
                '' :  urlencode($this->_username) . ':' . urlencode($this->_password) . '@') . urlencode($this->_host);
        }        

        return $str . '/' . $path;
    }

    public function upload($localPath, $remotePath = '', $mode = '0777', $overwrite = FALSE)
    {
        if($remotePath === '') return FALSE;

        if(!file_exists($localPath)) throw new HandlerException('file not existes', 100);

        $fp1 = @fopen($localPath, 'r');

        if(!is_resource($fp1)) throw new HandlerException('local file can\'t open', 101);

        $opts = array('ftp' => array(
            'overwrite' => $overwrite
        ));

        $context = stream_context_create($opts);

        $streamStr = $this->buildStreamString('ftp', $remotePath);

        $fp2 = @fopen($streamStr, 'w', FALSE, $context);

        if(!is_resource($fp2)) throw new HandlerException('remote file can\'t open or already exists', 102);

        stream_copy_to_stream($fp1, $fp2);

        fclose($fp2);
        fclose($fp1);
        
        return array('path' => $remotePath);

    }

    /**
     * 修改文件 [相当于删除文件然后重新上传一份]
     * 
     * @param  [type] $localPath  [description]
     * @param  [type] $remotePath [description]
     * @return [type]             [description]
     */
    public function modify($localPath, $remotePath, $mode = '0777')
    {
        return $this->upload($localPath, $remotePath, TRUE, $mode);
    }

    public function append($localPath, $remotePath)
    {

        if(!file_exists($localPath)) throw new HandlerException('file not existes', 100);

        $fp1 = @fopen($localPath, 'r');

        if(!is_resource($fp1)) throw new HandlerException('local file can\'t open', 101);

        $opts = array('ftp' => array(
            'overwrite' => FALSE
        ));

        $context = stream_context_create($opts);

        $streamStr = $this->buildStreamString('ftp', $remotePath);
        $fp2 = @fopen($streamStr, 'a', FALSE, $context);

        if(!is_resource($fp2)) throw new HandlerException('remote file can\'t open', 102);

        stream_copy_to_stream($fp1, $fp2);

        fclose($fp2);
        fclose($fp1);

        
        return array('path' => $remotePath);
    }

    /**
     * 下载文件[不要使用在大文件身上 很容易内存用光]
     * 
     * @param  [type] $remotePath [description]
     * @return [type]             [description]
     */
    public function download($remotePath)
    {
        return @file_get_contents($this->buildStreamString('ftp', $remotePath));
    }

    /**
     * 删除文件
     * 
     * @param  [type] $remotePath [description]
     * @return [type]             [description]
     */
    public function delete($remotePath)
    {
        
        return @unlink($this->buildStreamString('ftp', $remotePath));
    }

    /**
     * 获取文件的一些元信息
     * 
     * @param  [type] $remotePath [description]
     * @return [type]             [description]
     */
    public function getMetaInfo($remotePath)
    {
        return @stat($this->buildStreamString('ftp', $remotePath));
    }

    /**
     * 设置文件元信息[这个版本无法使用]
     */
    public function setMetaInfo($remotePath, $data)
    {
        return FALSE;
    }

    public function rmdir($remotePath)
    {
        return @rmdir($this->buildStreamString('ftp', $remotePath));
    }

    public function isDirExists($dir)
    {
        return is_dir($this->buildStreamString('ftp', $dir));
    }

    public function mkdir($dir, $mode = 0777, $recursion = FALSE)
    {
        return mkdir($this->buildStreamString('ftp', $dir), $mode, $recursion);
    }
}