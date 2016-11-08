<?php
/**
 * @author wuzhiqiang <qpwoeiru96@gmail.com>
 * @todo 代码还需要优化
 * 
 * 原版权如下
 * 
 * 将一个汉字(GBK)转化成拼音(暂不区分多音字)
 * 作者: 马明练(!hightman)
 * 主页: http://php.twomice.net
 * 源码及演示: http://scws.tguanlim.com/py/getpy.php
 * 
 */

namespace common\extensions\PinyinConverter;

class FastConverter implements ConverterInterface
{
    private $_dbPath = __DIR__;

    private $_dbName = 'pinyin.dat';

    private $_fd = NULL;

    public function __construct()
    {
        $this->_fd = @fopen($this->_dbPath . DIRECTORY_SEPARATOR . $this->_dbName, 'rb');
        if(!$this->_fd) 
            throw new Exception("unable to load PinYin data file `{$this->_dbName}`");
    }

    public function init(array $params = array())
    {
        foreach($params as $key => $val) {
            $name = '_' . $key;
            if(property_exists($this, $name)) $this->$name = $val;
        }
    }

    /**
     * 单个转换
     * 
     * @param string $zh 
     * @return string
     */
    private function _singleConvert($zh) 
    {

        //if (strlen($zh) != 2) throw new Exception("`$zh` is not a valid GBK hanzi");
 
        $high = ord($zh[0]) - 0x81;
        $low  = ord($zh[1]) - 0x40;
        
        // 计算偏移位置
        $nz   = (ord($zh[0]) - 0x81);
        $off  = ($high << 8) + $low - ($high * 0x40);
 
        // 判断 off 值
        if ($off < 0) return $zh;
        //throw new Exception("`$zh` is not a valid GBK hanzi-2");
 
        fseek($this->_fd, $off * 8, SEEK_SET);
        $ret = fread($this->_fd, 8);
        $ret = unpack('a8py', $ret);
        return trim($ret['py']);
    }

    /**
     * 转换字符串
     * $str 必须为UTF-8编码
     * 
     * @param string $str [description]
     * @return [type]      [description]
     */
    private function _convert($str, $justInitial = FALSE)
    {

        $str = mb_convert_encoding($str, 'GBK', 'UTF-8');
        $len = strlen($str);
        $ret = '';

        for ($i = 0; $i < $len; $i++) {

            if (ord($str[$i]) > 0x80) {

                $xx = $justInitial ? 
                    substr($this->_singleConvert(substr($str, $i, 2)), 0, 1) : 
                    $this->_singleConvert(substr($str, $i, 2));

                $xx = preg_replace('/[0-9]/', '', $xx);

                $ret .= ($xx ?  $xx : substr($str, $i, 2));
                $i++;

            } else {
                $ret .= $str[$i];
            }
        }

        return $ret;

    }

    public function getPinyin($str)
    {
        return $this->_convert($str);
    }

    public function getInitial($str)
    {
        return $this->_convert($str, TRUE);
    }

}