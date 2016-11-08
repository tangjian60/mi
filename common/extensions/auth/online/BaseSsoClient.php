<?php
namespace common\extensions\auth;
use \CComponent;
use \Yii;
use common\components\FlagManager;
/**
 * BaseSSoClient
 */
abstract class BaseSsoClient extends CComponent{
	
	/**
	 * @var string ticket票据
	 */
	protected $_ticket;
	/**
	 * @var string 用户userid
	 */
	protected $_userid;
	/**
	 * @var string 用户类型
	 */
	protected $_userType;
	/**
	 * @var string 用户名
	 */
	protected $_username;
	/**
	 * @var string 票据状态码
	 */
	protected $_ticketFlag;
	
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
	 * init
	 * @param string $ticket
	 */
	function init($ticket = '')
	{
		$ticket = $ticket ? $ticket : $this->getCookie('ticket');
		if ($ticket != $this->_ticket)
		{
			$this->_ticket = $ticket;
			$this->_userid = NULL;
			$this->_userType = NULL;
			$this->_username = NULL;
			$this->initTicketFlag();
		}
	}
	
	/**
	 * 初始化ticket的flag
	 */
	abstract function initTicketFlag();
	
	/**
	 * 获取用户userid
	 */
	abstract function getId();
	
	/**
	 * 获取用户名username
	 */
	abstract function getUsername();
	
	/**
	 * 获取用户类型userType
	 */
	abstract function getUserType();
	
	/**
	 * 获取cookie
	 *
	 * @param string 要获取的key键
	 * @return 键为空时返回cookie数组，不为空时返回cookie值
	 */
	function getCookie($key = '')
	{
		$cookie = Yii::app()->request->getCookies();
		return ($key == '') ? $cookie : (isset($cookie[$key]) ? $cookie[$key] : '');
	}
	
	/**
	 * 魔术方法 ，方便获取字段,$ssoClient->useid时可以直接获取userid字段
	 * @param string
	 * @return string
	 */
	function __get($name)
	{
		switch ($name)
		{
			case 'userid':
				return $this->getId();
				break;
			case 'username':
				return $this->getUsername();
				break;
			case 'userType':
				return $this->getUserType();
				break;
			default:
				$protectedName = '_'.$name;
				if (isset($this->$protectedName) && $this->$protectedName !== NULL)
				{
					return $this->$protectedName;
				}
				$this->$protectedName = $this->getField($name);
				return $this->$protectedName;
				break;
		}
	}
}
