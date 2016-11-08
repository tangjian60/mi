<?php
$params = array_merge(
		require(__DIR__ . '/../../communal/config/params.php'),
		require(__DIR__ . '/../../communal/config/params-local.php'),
		require(__DIR__ . '/params.php'),
		require(__DIR__ . '/params-local.php')
);
return [
    'id' => 'app-mi',
    'language' => 'zh_CN',
    'basePath' => dirname(__DIR__),
    'homeUrl' => '/credit',
    //'bootstrap' => ['log'],
    'controllerNamespace' => 'mi\controllers',
    'modules' => [
        'credit' => [
            'class' => 'mi\modules\credit\Credit',
        ],
    ],
    'components' => [
        //'request' => ['baseUrl' => '/credit'],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
	
    	'i18n' => [
    				'translations' => [
    						'*' => [
    								'class' => 'yii\i18n\PhpMessageSource',
    								//'basePath' => '/messages',
    								'fileMap' => [
    										'common' => 'common.php',
    										'test' => 'test.php'
    								],
    						],
    				],
    		],
        'authManager' => [
            'class' => 'communal\components\AuthManager',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.1.199;dbname=76mi',
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
        ],  
    
    		'user' => [
    				'identityClass' => 'mi\modules\credit\models\MiBduser',
    				//'enableAutoLogin' => true,
    				//'loginUrl' => ['site/login']
    		],
  
          'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];
