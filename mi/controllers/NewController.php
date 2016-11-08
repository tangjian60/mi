<?php
namespace mi\controllers;

use Yii;
use mi\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use mi\modules\credit\models\MiCategory;
/**
 * Site controller
 */
class NewController extends Controller
{
	public $layout = 'fmain';
  
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
    
        return [
            'access' => [
                'class' => AccessControl::className(),
              //  'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        //'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        //'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
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
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
    	$params = Yii::$app->request->get();
    	$MiCategory = MiCategory::find()->where('id='.$params['id'])->one();
    	return $this->render('index',['MiCategory' => $MiCategory]);
    }
    public function actionClock(){
    	return $this->render('clock');
    }
    public function actionHuangli(){
    	return $this->render('huangli');
    }
    
}
