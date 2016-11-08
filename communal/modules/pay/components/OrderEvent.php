<?php
namespace communal\modules\pay\components;

use yii\base\Event;

class OrderEvent extends Event
{
    public $order_num;
    public $params;
}
