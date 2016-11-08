<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace communal\components\ad;

use Yii;
use Closure;
use yii\base\Behavior;
use yii\base\Event;

class AdBehavior extends Behavior
{

    public $eventPosition = [];

    public $value;

    public function events()
    {
        return array_fill_keys($this->eventPosition, 'handle');
    }

    public function handle($event)
    {
        $this->setData( $this->processData() );
    }

    public function setData($value)
    {
        $this->owner->setData($value);
    }

    public function getData()
    {
        return $this->owner->getData();
    }

    public function processData()
    {
        return $this->getData();
    }
}
