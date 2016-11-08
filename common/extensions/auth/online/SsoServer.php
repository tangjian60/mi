<?php
namespace common\extensions\auth;
use common\helpers\HttpHelper;
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
		$params = array_merge($default, $params);
		$result = HttpHelper::post(self::$_ssoLoginServer, $params);
		$data	= CJSON::decode($result);
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
		$params = array_merge($default, $params);
		$result = HttpHelper::post(self::$_ssoRegisterServer, $params);
		$data	= CJSON::decode($result);
		return reset($data);
	}
	
}
