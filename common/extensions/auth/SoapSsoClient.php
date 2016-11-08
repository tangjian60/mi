<?php
namespace common\extensions\auth;

use \Yii;
use \SoapClient;
use \common\components\FlagManager;
use \common\extensions\auth\BaseSsoClient;
use \common\helpers\ConfigHelper;

/**
 * SoapSSoClient
 * 基于ticket的用户操作，通过webservice调用接口
 *
 */
class SoapSsoClient extends BaseSsoClient{
	
	/**
	 * @var string webservice的wsdl地址
	 */
	public $wsdlUrl	= 'http://sso.veryeast.cn/soap_api/ticket?wsdl';
	/**
	 * @var object SoapClient对象
	 */
	protected $_soapClient;
	
	/**
	 * 构造函数，票据没有传入时默认从cookie中读取
	 *
	 * @param string 票据ticket
	 * @return void
	 */
	function __construct($ticket = '')
	{
		$this->_ticket = $ticket ? $ticket : $this->getCookie('ticket');
		$this->initTicketFlag();
	}
	
	/**
	 * 初始化ticket的flag
	 *
	 * @return void
	 */
	function initSoapClient()
	{
		$wsdlUrl = $this->wsdlUrl;
		$this->_soapClient = new SoapClient($wsdlUrl);
	}
	
	/**
	 * 初始化ticket的flag
	 *
	 * @return void
	 */
	function initTicketFlag()
	{
		if (empty($this->_ticket))
		{
			$this->_ticketFlag = FlagManager::FLAG_TICKET_EMPTY;
		}
		else
		{
			$res = $this->callFunction('check_ticket');
			$this->_ticketFlag = reset($res);
		}
		
		if ($this->_ticketFlag != FlagManager::FLAG_TICKET_IS_VALID)
		{
			$this->_userid = 0;
			$this->_userType = 0;
			$this->_username = '';
		}
	}
	
	/**
	 * 获取用户userid
	 *
	 * @return userid
	 */
	function getId()
	{
		if ($this->_userid !== NULL)
		{
			return $this->_userid;
		}
		$this->_userid = $this->getField('userid');
		return $this->_userid;
	}
	
	/**
	 * 获取用户类型userType 
	 *
	 * @return userType
	 */
	function getUserType()
	{
		if ($this->_userType !== NULL)
		{
			return $this->_userType;
		}
		$this->_userType = $this->getField('user_type');
		return $this->_userType;
	}
	
	/**
	 * 获取用户名username
	 *
	 * @return username
	 */
	function getUsername()
	{
		if ($this->_username !== NULL)
		{
			return $this->_username;
		}
		$this->_username = $this->getField('username');
		return $this->_username;
	}
	
	/**
	 * 获取用户信息
	 * @example $this->getField('email');	=>	'aa@aa.com';
	 * 
	 * @param string 用户字段  email,mobile,truename,gender,birthday,password_question等
	 * @return string 字段值
	 */
	function getField($column)
	{
		if ($this->_ticketFlag == FlagManager::FLAG_TICKET_IS_VALID)
		{
			$res = $this->callFunction('get_field', array($column));
			return reset($res);
		}
	}
	
	/**
	 * 设置用户信息
	 * @example $this->setField('truename', 'aa');	=>	0;
	 *
	 * @param string 用户字段  email,mobile,truename,gender,birthday,password_question等
	 * @return string 返回码flag 0表示执行成功
	 */
	function setField($column, $value = '', $resupply = '')
	{
		$res = $this->callFunction('set_field', array($column, $value, $resupply));
		return reset($res);
	}
	
	/**
	 * 批量获取用户信息
	 * @example $this->getFields(array('email','truename'));	=>	array('email'=>'aa@aa.com', 'truename'=>'aa');
	 *
	 * @param array 用户字段数组 array('email','mobile','truename','gender','birthday','password_question')
	 * @return array 字段值数组
	 */
	function getFields($params = array())
	{
		$res = array();
		if( ! empty($params) && is_array($params))
		{
			foreach ($params as $value)
			{
				$res[$value] = $this->getField($value);
			}
		}
		return $res;
	}
	
	/**
	 * 修改密码
	 * @example $this->editPassword('123456','1234567');	=>	0
	 *
	 * @param string 旧密码
	 * @param string 新密码
	 * @return string 返回码flag 0表示执行成功
	 */
	function editPassword($oldPassword, $newPassword)
	{
		$res = $this->callFunction('edit_password', array($oldPassword, $newPassword));
		return reset($res);
	}
	
	/**
	 * 设置密保问题答案
	 * @example $this->setQuestion('123456','1+1=?','2');	=>	0
	 *
	 * @param string 密码
	 * @param string 问题
	 * @param string 答案
	 * @return string 返回码flag 0表示执行成功
	 */
	function setQuestion($password, $question, $answer)
	{
		$res = $this->callFunction('set_question', array($password, $question, $answer));
		return reset($res);
	}
	
	/**
	 * 申请邮箱验证
	 * @example $this->applyEmailVerify()
	 *
	 * @return string 返回码flag 0表示执行成功
	 */
	function applyEmailVerify()
	{
		return $this->callFunction('apply_email_verify');
	}
	
	/**
	 * 调用webservice方法 
	 * 
	 * @param string 要执行的方法function
	 * @param string 参数，以数组形式
	 * @return void
	 */
	function callFunction($function = '', array $params = array())
	{
		if ($this->_soapClient == NULL)
		{
			$this->initSoapClient();
		}
		$params = array_merge(array($this->_ticket), array_values($params));
		return call_user_func_array(array($this->_soapClient, $function), $params);
	}
	
}
