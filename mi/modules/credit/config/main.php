<?php
return [
		'id' => 'app-mi',
		'language' => 'zh-CN',
		'sourceLanguage' => 'en-US',
    'components' => [
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'mi\modules\credit\models\MiBduser',
            'loginUrl' => '/credit/index',
            //'enableAutoLogin' => true,
        ],
    	
    ],
		
];
