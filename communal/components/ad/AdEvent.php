<?php
namespace communal\components\ad;

use yii\base\Event;

class AdEvent extends Event
{
    public $placeId;
    public $dataType;
    /** @var array exclude company ids */
    public $excludeUids = [];

    /**
     * @var $processData the data that need to process
     */
    public $processData;
}
