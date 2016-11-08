<?php

namespace common\extensions\auth;

use \common\helpers\HttpHelper;
use \common\helpers\ConfigHelper;
use \CJSON;
use \Yii;

class SsoServer
{
	
	public static $_ssoLoginServer		= 'http://sso.veryeast.cn/user/login';
	
	public static $_ssoRegisterServer	= 'http://sso.veryeast.cn/user/register';
	
	/**
	 * 服务端登录login
	 *
	 * @param	array $param 登录数据
	 * 
	 * $param = array('username'=>'wuhaidong', 'password'=>123456);
	 * SsoServer::login($param);
	 * 
	 * result:
	 * 验证成功:
	 * array('flag'=>'0','userid'=>1425423,'ticket'=>'7b0beMAMltThWQNxv2LYuMgXJJDS0hGZ6KOD/h5NvxxDISDrjwgn1cRRsbH47DHPRtzRE1DRgZCGVZ9l74r8','user_type'=>2) 
	 * 验证失败:
	 * array('flag'=>1033) 
	 * flag为0时成功，不为0时含义详见文档
	 * 
	 * @return	array 
	 */
	public static function login($params = array())
	{
		$default = array(
			'return_type'	=> 'json',
			'unset_cookie'	=> 1,
			'encoding'		=> 'utf-8',
			'ip'			=> Yii::app()->request->userHostAddress,
		);
		
		//$server = ConfigHelper::adjustHost(self::$_ssoLoginServer);
		$server = self::$_ssoLoginServer;
		
		$params = array_merge($default, $params);
		$result = HttpHelper::post($server, $params);
		$data	= CJSON::decode($result);
		$data	= empty($data) ? array() : $data;
		
		return reset($data);
	}
	
	/**
	 * 服务端注册register
	 *
	 * @param	array $param 注册数据
	 *
	 * $param = array('username'=>'wuhaidong', 'password'=>123456, 'email'=>'aa@aa.com', 'user_type'=>2);
	 * SsoServer::register($param);
	 *
	 * result:
	 * 注册成功:
	 * array('flag'=>'0','userid'=>1425423,'ticket'=>'7b0beMAMltThWQNxv2LYuMgXJJDS0hGZ6KOD/h5NvxxDISDrjwgn1cRRsbH47DHPRtzRE1DRgZCGVZ9l74r8','user_type'=>2)
	 * 注册失败:
	 * array('flag'=>1002)
	 * flag为0时成功，不为0时含义详见文档
	 *
	 * @return	array
	 */
	public static function register($params = array())
	{
		$default = array(
			'return_type'	=> 'json',
			'user_type'		=> 2,
			'unset_cookie'	=> 1,
			'encoding'		=> 'utf-8',
			'ip'			=> Yii::app()->request->userHostAddress,
			'register_page_source' => Yii::app()->request->hostInfo
		);
		
		$server = ConfigHelper::adjustHost(self::$_ssoRegisterServer);
		
		$params = array_merge($default, $params);
		$result = HttpHelper::post($server, $params);
		$data	= CJSON::decode($result);
		
		return reset($data);
	}
	
	/**
	 * 手机号码注册
	 * 调用前，验证码验证要在注册前进行验证，后面流程不会进行验证
	 * 
	 * @param string 手机号码
	 * @param string 验证码
	 * @param string 密码
	 * @return json 注册返回
	 */
	public static function mobileReg($params){
		
		$default = array(
				'key' => md5(md5('veryeast')),//服务器端注册key
				'return_type'	=> 'json',
				'user_type'		=> 2,
				'unset_cookie'	=> 1,
				'unsend_email'  => 1,
				'encoding'		=> 'utf-8',
				'ip'			=> Yii::app()->request->userHostAddress,
				'register_page_source' => Yii::app()->request->hostInfo,
				'register_type' =>'mobile_register'
		);
	
		$server = ConfigHelper::adjustHost(self::$_ssoRegisterServer);
		
		$params = array_merge($default, $params);
		$result = HttpHelper::post($server, $params);
		$data	= (array)CJSON::decode($result);
		
		return reset($data);
	}
	
	/**
	 * 获取验证码
	 */
	public static function getCode($mobile, $smsType){
		$params = array(
				'return_type'	=> 'json',
				'user_type'		=> 2,
				'unset_cookie'	=> 1,
				'unsend_email'  => 1,
				'encoding'		=> 'utf-8',
				'mobile'        => $mobile,
				'sms_type'       => $smsType
		);
		
		$server = 'http://sso.veryeast.cn/user/code';
		$result = HttpHelper::post($server, $params);
		$data	= (array)CJSON::decode($result);
		
		return reset($data);
	}
	
	/**
	 * 重新发送激活邮件
	 */
	public static function reSend(){
		$params = array(
				'return_type'	=> 'json',
				'unset_cookie'	=> 1,
				'encoding'		=> 'utf-8',
				'ip'			=> Yii::app()->request->userHostAddress
		);
	
		$server = 'http://sso.veryeast.cn/http_api/reSend';
	
		$result = HttpHelper::post($server, $params);
		$data	= CJSON::decode($result);
	
		$data	= empty($data) ? array() : $data;
	}
	
}
