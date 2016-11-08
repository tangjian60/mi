<?php
/**
 * 自动加载mongodbORm
 */
namespace common\extensions\MongoYii;

class EMongoAutoLoad
{
    public static function autoload($className)
    {
        if (substr($className, 0, 6) == 'EMongo') {
            if (strpos($className, 'Behaviour') !== false) {
                include_once(__DIR__ . '/behaviors/' . $className . '.php');
            } elseif (strpos($className, 'Validator') !== false) {
                include_once(__DIR__ . '/validators/' . $className . '.php');
            } else {
                include_once(__DIR__ . '/' . $className . '.php');
            }
        }

        if($className = 'ESubdocumentValidator') {
            include_once(__DIR__ . '/validators/' . $className . '.php');
        }
    }
}