<?php
namespace communal\extensions;

class Storage
{

    public static function getHandler($handler, $params = array())
    {        
        $className =  __NAMESPACE__ . '\\storage\\' . strtoupper($handler) . 'Handler';

        if(!class_exists($className)) {
            throw new \Exception($handler . 'Handler is not exists', 404);
        }

        $instance =  new $className;
        $instance->init($params);

        return $instance;
    }
}



