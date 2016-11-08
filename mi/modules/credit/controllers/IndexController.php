<?php
namespace mi\modules\credit\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

//use common\extensions\PHPExcel;

/**
 * Index controller
 */
class IndexController extends Controller
{
    public $enableCsrfValidation = false;
    const ADJUST_RULE_ID = 49;

    /**
     * @inheritdoc
     */
 public function behaviors()
    {
    	
    	return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
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
    	
     	if(!isset(Yii::$app->user->identity->id)){
    		return $this->redirect('/site/login');
    	} 
    	
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

    public function actionLogout()
    {
    	Yii::$app->user->logout();
    
    	return $this->goHome();
    }
    
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex(){
        return $this->render('index');  
    }
    
    public function actionList(){
    	return $this->render('list');
    }
    public function actionCompetency(){
    	 return $this->render('competency');
    }
    public function actionAdd(){
    	$this->display();
    }













    
}
