<?php

namespace communal\modules\authorization\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Client controller
 */
class ClientController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'captcha', 'error'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'communal\modules\authorization\controllers\actions\CaptchaAction',
                //'fixedVerifyCode' => YII_ENV_DEV ? 'tss' : null,
                'minLength' => 4,
                'maxLength' => 5,
                'isClient' => true,
                'isDisturb' => true,
            ],
            'login' => [
                'class' => 'communal\modules\authorization\controllers\actions\LoginAction',
                'from' => 'client',
            ]
        ];
    }


}
