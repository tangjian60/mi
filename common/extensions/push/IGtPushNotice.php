<?php
namespace common\extensions\push;
use \Yii;
use \IGeTui;
use \IGtTransmissionTemplate;
use \IGtSingleMessage;
use \IGtTarget;
use \IGtLinkTemplate;
use \IGtListMessage;
use \IGtNotificationTemplate;

require __DIR__ . '/libs/IGt.Push.php';

/**
* @desc	安卓推送的接口程序
*/
class IGtPushNotice {
	
	public $host, $app_id, $master_secret, $app_key, $logo;
	
	public function __construct($app_id = '', $app_key = '', $master_secret = '', $logo = '', $host='http://sdk.open.api.igexin.com/apiex.htm')
	{
		$this->host = $host;
		$this->app_id = $app_id;
		$this->master_secret = $master_secret;
		$this->app_key = $app_key;
		$this->logo = $logo;
	}
	
	public function __destruct()
	{
	}
   
	/**
	* @desc	向单个客户端推送消息
	*/
	public function pushMessageToSingle(){
		$igt = new IGeTui($this->host,$this->app_key,$this->master_secret);
		
		//消息类型 : 透传信息
		$template =  new IGtTransmissionTemplate(); 

		$template ->set_transmissionType(2);//透传消息类型
		$template ->set_appId($this->app_id);//应用appid
		$template ->set_appkey($this->app_key);//应用appkey
		$template ->set_transmissionContent("测试离线");//透传内容

		//个推信息体
		$message = new IGtSingleMessage();

		$message->set_isOffline(true);//是否离线
		$message->set_offlineExpireTime(3600*12);//离线时间
		$message->set_data($template);//设置推送消息类型

		//接收方
		$target = new IGtTarget();
		$target->set_appId($this->app_id);
		$target->set_clientId('3a3fec54506bc7b3f76ef42241b39281');

		$rep = $igt->pushMessageToSingle($message,$target);

		//var_dump($rep);
	}
	
	/**
	* @desc	向多个用户推送消息
	**/
	function pushMessageToList($data){
		$igt = new IGeTui($this->host,$this->app_key,$this->master_secret);

		//消息类型 :状态栏链接 点击通知打开网页 
		$template =  new IGtNotificationTemplate(); 
		
		$template ->set_appId($this->app_id);//应用appid
		$template ->set_appkey($this->app_key);//应用appkey
		$template->set_transmissionType(2);//透传消息类型
		$template ->set_title($data['title']);//通知栏标题
		$template ->set_text($data['content']);//通知栏内容
		$template ->set_logo($this->logo);//通知栏logo
		//$template->set_setTransmissionType(2);//立即打开应用
		if(isset($data['readingid'])){
			$template->set_transmissionContent(strval($data['readingid']));//文章ID
		}
		
		//$template->set_transmissionContent($data['readingid']);//文章ID
		$template ->set_isRing(true);//是否响铃
		$template ->set_isVibrate(true);//是否震动
		$template ->set_isClearable(true);//通知栏是否可清除
		//$template ->set_url("http://www.igetui.com/");//打开连接地址

		//个推信息体
		$message = new IGtListMessage();

		$message->set_isOffline(true);//是否离线
		$message->set_offlineExpireTime(3600*12);//离线时间
		$message->set_data($template);//设置推送消息类型
		
		$contentId = $igt->getContentId($message);

		//接收方1	
		$target1 = new IGtTarget();
		$target1->set_appId($this->app_id);
		$target1->set_clientId($data['android_clientid']);
		
/* 		//接收方2
		$target2 = new IGtTarget();
		$target2->set_appId($this->app_id);
		$target2->set_clientId('465a605af8697072df931629fe89978c'); */
		
		$targetList[] = $target1;
		//$targetList[] = $target2;

		$igt->pushMessageToList($contentId, $targetList);

		return true;

	}

	/**
	* @desc	向App推送消息
	*
	*/
	function pushMessageToApp(){
		$igt = new IGeTui($this->host,$this->app_key,$this->master_secret);
		
		//消息类型 : 状态栏通知 点击通知启动应用
		$template =  new IGtNotificationTemplate(); 

		$template->set_appId($this->app_id);//应用appid
		$template->set_appkey($this->app_key);//应用appkey
		$template->set_transmissionType(2);//透传消息类型
		$template->set_transmissionContent("测试离线");//透传内容
		$template->set_title("个推");//通知栏标题
		$template->set_text("个推最新版点击下载");//通知栏内容
		$template->set_logo("http://wwww.igetui.com/logo.png");//通知栏logo
		$template->set_isRing(true);//是否响铃
		$template->set_isVibrate(true);//是否震动
		$template->set_isClearable(true);//通知栏是否可清除

		//基于应用消息体
		$message = new IGtAppMessage();

		$message->set_isOffline(true);
		$message->set_offlineExpireTime(3600*12);
		$message->set_data($template);

	 
		$message->set_appIdList(array($this->app_id));
		$message->set_phoneTypeList(array('ANDROID'));
		$message->set_provinceList(array('浙江','北京','河南'));

		$rep = $igt->pushMessageToApp($message);

		//var_dump($rep);
	}	


}
