<?php
namespace communal\components;

use Yii;
use yii\base\Component;
use yii\caching\MemCache;
use yii\base\InvalidParamException;
use yii\base\InvalidCallException;

class BaseComponent extends Component
{
    protected static $prefixKey = __NAMESPACE__;

    /**
     * 获取 memcache 组件
     * @return object
     */
    public static function getCacheCompoent()
    {
        if( ! Yii::$container->has('memcache') ){
            $config = Yii::getConfig('memcache.api');
            list($host, $port) = explode(':', $config);
            $config = array(
                'class' => MemCache::className(),
                'useMemcached' => !(defined('YII_ENV') && (YII_ENV === 'dev')),
                'servers' => array(
                    array(
                        'host' => $host,
                        'port' => $port,
                        'weight' => 60,
                    ),
                ),
                'keyPrefix' => self::$prefixKey,
            );

            Yii::$container->setSingleton('memcache', $config);
        }
        
        return Yii::$container->get('memcache');
    }

     /**
     * 获取cache值
     * @param string $id
     */
    public static function getCache($id)
    {
        return self::getCacheCompoent()->get($id);
    }
    
    /**
     * 设置cache
     * 
     * @param string $id
     * @param mixed $value
     * @param int $expire
     */
    public static function setCache($id, $value, $expire = 0)
    {
        return self::getCacheCompoent()->set($id, $value, $expire);
    }
    
    /**
     * 删除cache
     * @param string $id
     */
    public static function deleteCache($id)
    {
        return self::getCacheCompoent()->delete($id);
    }
}