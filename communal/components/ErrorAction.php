<?php

namespace communal\components;

use Yii;
use yii\base\Action;

/**
 * Error Action
 */
class ErrorAction extends Action
{
    public $view = '@communal/views/error/error.php';

    public function run()
    {
        return $this->controller->renderPartial($this->view);
    }
}
