<?php

namespace communal\modules\gii;

class Gii extends \yii\gii\Module
{
    public $controllerNamespace = 'communal\modules\gii\controllers';

    protected function coreGenerators()
    {
        return [
            'model' => ['class' => 'communal\modules\gii\generators\model\Generator'],
            'crud' => ['class' => 'yii\gii\generators\crud\Generator'],
            'controller' => ['class' => 'yii\gii\generators\controller\Generator'],
            'form' => ['class' => 'yii\gii\generators\form\Generator'],
            'module' => ['class' => 'yii\gii\generators\module\Generator'],
            'extension' => ['class' => 'yii\gii\generators\extension\Generator'],
        ];
    }
    
}
