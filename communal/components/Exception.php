<?php

namespace communal\components;


class Exception extends \yii\base\Exception
{
    public function __construct($message, $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        if(trim($this->message) === '' && $code !== 0) {
            $this->message = I18N::t($code);
        }
    }

}