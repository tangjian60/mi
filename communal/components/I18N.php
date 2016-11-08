<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/30
 * Time: 16:35
 */

namespace communal\components;

use Yii;

class I18N
{
    const EXT = '.php';

    public static $language = 'zh_cn';

    private static $_data   = array();

    private static $_instance = NULL;

    private function __construct()
    {
        self::$language = strtolower(Yii::$app->language);

//        self::$_data = include( __DIR__ . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . self::$language . self::EXT );
        self::$_data = Yii::getConfig('','@communal/config/data/i18n/'.self::$language.self::EXT);
    }

    public static function getInstance()
    {
        if(self::$_instance === NULL)
            self::$_instance = new self;
        return self::$_instance;
    }

    public static function t($id)
    {
        self::getInstance();
        if(isset(self::$_data[$id])) {

            $params = func_get_args();
            $params[0] = self::$_data[$id];
            return call_user_func_array('sprintf', $params);

        } else return $id;

    }
}