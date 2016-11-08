<?php

namespace communal\modules\authorization;

class Authorization extends \yii\base\Module
{
    public $controllerNamespace = 'communal\modules\authorization\controllers';
    public $defaultRoute = 'client';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
