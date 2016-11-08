<?php

namespace mi\controllers;
use yii\data\Pagination;
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
use mi\modules\credit\models\MiSearch;
use mi\modules\credit\models\MiOpinion;
use mi\modules\credit\models\MiCe;
/**
 * Site controller
 */
class IndexController extends Controller
{
	public $layout = 'fmain';

	public $enableCsrfValidation = false;
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
    	header("Content-type: text/html; charset=utf-8");
    	$hotsearchtop = $this->getWebDiv('class="hotsearchtop"','https://www.hao123.com/');
    	$joke = $this->getWebDiv('id="joke"','https://hao.360.cn/');
    	$slide = $this->getWebDiv('class="slide-content"','https://hao.360.cn/');
    	$qingtiancms = $this->getWebDiv('class="col"','http://www.hao360.in/');
    	$MiSearch = MiSearch::find()->limit(2)->all();
        return $this->render('index',['hotsearchtop' => $hotsearchtop,'qingtiancms' =>$qingtiancms,'joke' => $joke, 'slide' => $slide,'MiSearch' => $MiSearch]);
    }
    public function actionShouyexiufu(){
    	return $this->render('shouyexiufu');
    }
    public function actionAboutus(){
    	return $this->render('aboutus');
    }
    public function actionCalculate(){
    	return $this->render('calculate');
    }

    //cnzz导入
    public function actionIm($filePath='',$bduser_id=null,$unitPrice=0.5){
    	
    	$nm = Yii::$app->request->get()['t'];
    	$type = Yii::$app->request->get()['type'];
    	set_time_limit(0);
    	$filePath = './ce/'."$nm".'.xlsx';
    	
    	$PHPExcel  = new \PHPExcel();
    	$PHPReader = new \PHPExcel_Reader_Excel2007();
    	if (!$PHPReader->canRead($filePath)) {
    		$PHPReader = new \PHPExcel_Reader_Excel5();
    		if (!$PHPReader->canRead($filePath)) {
    			$errorMessage = "Can not read file.";
    			return $this->render('error', ['errorMessage' => $errorMessage]);
    		}
    	}
    	$PHPExcel = $PHPReader->load($filePath);
    	$allSheet = $PHPExcel->getSheetCount(); // sheet数
    	$currentSheet = $PHPExcel->getSheet(0); // 拿到第一个sheet（工作簿？）
    	$allColumn = $currentSheet->getHighestColumn(2); // 最高的列，比如AU. 列从A开始
    	//yii::p($allColumn);
    	$allRow = $currentSheet->getHighestRow(); // 最大的行，比如12980. 行从0开始
    	for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
    		$result = [];
    		for ($currentColumn="B"; $currentColumn <= $allColumn; $currentColumn++) {
    			$val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65, $currentRow)->getValue(); // ord把字母转为ascii码。
    			array_push($result, $val);
    		}
    		$lineVal = [];
    		$val = $currentSheet->getCellByColumnAndRow(ord('A') - 65,$currentRow)->getValue(); // ord把字母转为ascii码
    		if(empty($val)){
    			break;
    		}
    		if($type == '3'){
    			$val = gmdate("Y-m-d H:i:s", \PHPExcel_Shared_Date::ExcelToPHP($val));//cnzz
    			$result['5'] =$result['5'];
    		}elseif ($type == '1'){
    			$result['5'] ='';
    		}elseif($type == '2'){
    			$val = gmdate("Y-m-d H:i:s", \PHPExcel_Shared_Date::ExcelToPHP($val));
    			$result['5'] ='手机助手';
    			$result['3'] = '';
    			$result['2'] = '';
    			$result['4'] = '';
    		}
    		array_push($lineVal, $val);
    		//yii::p($result);
    	 	\Yii::$app->db->createCommand()->insert('mi_ce', [
    				'addtime' => $lineVal['0'],
    				'browse' => $result['0'],
    				'visitors' => $result['1'],
    				'ip'=> $result['2'],
    				'nvisitors' => $result['3'],
    	 			'visits'=> $result['4'],
    	 			'type' => $type,
    	 			'name' => $result['5'],//cnzz
    	 			//'region' => substr($result['5'],-5),
    		])->execute(); 	
    	}
    	
    }
    //cnzz模板
    public function actionCe($addtime='',$endtime='',$name='',$region=''){
    	$arn = Yii::$app->request->get();	
    	$mice = MiCe::find()->where("type='3' and name='".$arn['name']."' and addtime>='".$arn['addtime']." 00:00:00' and addtime<='".$arn['endtime']." 00:00:00'")->orderBy('id DESC')->all();
    	
    	$micout = MiCe::find()->where("type='3' and name='".$arn['name']."' and addtime>='".$arn['addtime']." 00:00:00' and addtime<='".$arn['endtime']." 00:00:00'")->select(['SUM(browse) as browse,sum(visitors) as visitors,sum(nvisitors) as nvisitors,sum(ip) as ip,sum(visits) as visits'])->one(); 
    	
    	return $this->render('cnzz2',['mice' => $mice,'addtime' => $addtime,'region'=>'','name'=>$name,'endtime' => $endtime,'micout' =>$micout->attributes]);
    	
    }
    //应用宝
    public function actionSys($addtime='',$endtime='',$browse=''){
    	$arn = Yii::$app->request->get();
    	
    	$mice = MiCe::find()->where("type='1' and browse='".$arn['browse']."' and addtime>='".$arn['addtime']."' and addtime<='".$arn['endtime']."'")->orderBy('id DESC')->all();
    	$micout = MiCe::find()->where("type='1' and browse='".$arn['browse']."' and addtime>='".$arn['addtime']."' and addtime<='".$arn['endtime']."'")->select(['sum(visitors) as visitors,sum(nvisitors) as nvisitors,sum(ip) as ip,sum(visits) as visits'])->one();
    	 
    	return $this->render('sys',['mice' => $mice,'addtime' => $addtime,'browse'=>$browse,'endtime' => $endtime,'micout' =>$micout->attributes]);
    	 
    }
    //360助手
    public function actionChannel($addtime='',$endtime='',$browse='',$page=''){
    	$arn = Yii::$app->request->get();
    	$query = MiCe::find()->where("type='2' and browse='".$arn['browse']."'and addtime>='".$arn['addtime']." 00:00:00' and addtime<='".$arn['endtime']." 00:00:00'")->orderBy('addtime DESC');
    	$micout = MiCe::find()->where("type='2' and browse='".$arn['browse']."' and addtime>='".$arn['addtime']." 00:00:00' and addtime<='".$arn['endtime']." 00:00:00'")->select(['sum(visitors) as visitors'])->one();
    	$countQuery = clone $query;
    	$pages = new Pagination(['totalCount' => $countQuery->count(),'defaultPageSize' =>10]);
    	$models = $query->offset($pages->offset)
    	->limit($pages->limit)
    	->all();
    	$scount=0;
    	foreach ($models as $val){
    		$scount+=$val['visitors'];
    	};
    	return $this->render('channel', [
    			'models' => $models,
    			'pages' => $pages,
    	 		'pagee' => $pages->page,
    	 		'totalCount' => $pages->totalCount,
    	 		'micout' => $micout,
    	 		'scount' => $scount,
    	 		'pagenum' => $pages->pageCount,
    	]); 
    }
    public function actionYijian(){
    	$model = new MiOpinion();
    	if ($model->load(Yii::$app->request->post()) && $model->save()) {
    		return $this->redirect(['yijian', 'id' => $model->id]);
    	} else {
    		return $this->render('yijian', [
    				'model' => $model,
    		]);
    	}
    	
    	return $this->render('yijian');
    }
    public function getWebDiv($div_id,$url=false,$tag='div',$data=false){
    	
        if($url !== false){
            $data = file_get_contents( $url );
        }
        preg_match_all('/<'.$tag.'/i',$data,$pre_matches,PREG_OFFSET_CAPTURE);    //获取所有div前缀
        preg_match_all('/<\/'.$tag.'/i',$data,$suf_matches,PREG_OFFSET_CAPTURE); //获取所有div后缀
     
        $hit = strpos($data,$div_id);
       
        if($hit == -1) return false;    //未命中
        $divs = array();    //合并所有div
        foreach($pre_matches[0] as $index=>$pre_div){
            $divs[(int)$pre_div[1]] = 'p';
            $divs[(int)$suf_matches[0][$index][1]] = 's';    
        }
       
        //对div进行排序
        $sort = array_keys($divs);
        asort($sort);
      
        $count = count($pre_matches[0]);
        foreach($pre_matches[0] as $index=>$pre_div){
            //<div $hit <div+1    时div被命中
            if(($pre_matches[0][$index][1] < $hit) && ($hit < $pre_matches[0][$index+1][1])){
                $deeper = 0;
                //弹出被命中div前的div
                while(array_shift($sort) != $pre_matches[0][$index][1] && ($count--)) continue;
                //对剩余div进行匹配，若下一个为前缀，则向下一层，$deeper加1，
                //否则后退一层，$deeper减1，$deeper为0则命中匹配，计算div长度
                foreach($sort as $key){
                    if($divs[$key] == 'p') $deeper++;
                    else if($deeper == 0) {
                        //$length = $key-$pre_matches[0][$index][1]-133;
                    	$length = $key-$pre_matches[0][$index][1];
                        break;
                    }else {
                        $deeper--;
                    }
                }
                $hitDivString = substr($data,$pre_matches[0][$index][1],$length).'</'.$tag.'>';
                break;
            }
        }
        return $hitDivString;
    }

}
