<?php

namespace mi\modules\credit;

use \Yii;

class Credit extends \yii\base\Module
{
    public $controllerNamespace = 'mi\modules\credit\controllers';
    public $layout = 'main';
    public $defaultRoute = 'index';

    public function init()
    {

        parent::init();
		
        //set user component property 
       // Yii::$app->user->loginUrl = ['/credit/xz-post-factors/Index'];
        
       
        Yii::configure($this->module, require(__DIR__ . '/config/main.php'));
   /*      echo '<pre>';
        print_r( $this->module->user );exit; */
      
    }
}
