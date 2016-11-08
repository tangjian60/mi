<?php
/**
 * @author  wuzhiqiang <qpwoeiru96@gmail.com>
 *
 * @todo API还需要完善 暴露更多的参数以及方法 临时用用可以 不建议正式环境使用 不支持多音字
 */
namespace common\extensions;

class PinyinConverter
{

    private static $_converter = NULL;

    public static $defaultConverter = 'FastConverter';

    private function __construct() 
    {

    }

    public static function getPinyin($str) 
    {
        if(self::$_converter === NULL) self::loadConverter(self::$defaultConverter);
        return call_user_func(array(self::$_converter, 'getPinyin'), $str);
    }

    public static function getInitial($str)
    {
        if(self::$_converter === NULL) self::loadConverter(self::$defaultConverter);
        return call_user_func(array(self::$_converter, 'getInitial'), $str);
    }

    public static function loadConverter($name, array $params = array())
    {
        $className = __NAMESPACE__ . '\\PinyinConverter\\' . $name;
        //\Yii::import($className);
        self::$_converter = new $className;
        self::$_converter->init($params);
    }



}