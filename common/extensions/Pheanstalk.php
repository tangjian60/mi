<?php
namespace common\extensions;

/**
 * 主动加载Pheanstalk的相关类
 */
\Yii::registerAutoloader(function($className) {

    $pheanstalkPath = __DIR__ . DIRECTORY_SEPARATOR . 'pheanstalk';

    $map = explode('_', $className);

    if($map[0] !== 'Pheanstalk') return FALSE;

    array_shift($map);

    $fileName = $pheanstalkPath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $map) . '.php';

    include $fileName;

    return TRUE;

});

class Pheanstalk extends \Pheanstalk_Pheanstalk {}