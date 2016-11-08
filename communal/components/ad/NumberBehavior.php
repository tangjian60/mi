<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace communal\components\ad;

use Yii;
use yii\base\Event;

class NumberBehavior extends AdBehavior
{
    public $eventPosition = [ Single::EVENT_END ];

    public function processData()
    {
        $data = $this->getData();
        if( count($data) > $this->value  ){
            shuffle($data);
            $data = array_slice($data, 0, $this->value);
        }
        return $data;
    }
}
