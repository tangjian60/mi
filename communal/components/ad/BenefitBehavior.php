<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace communal\components\ad;

use Yii;
use yii\base\Event;

class BenefitBehavior extends AdBehavior
{
    public $eventPosition = [ Single::EVENT_FILTER ];

    //@todo
    public function processData()
    {
        return $this->data;
    }
}
