<?php

namespace common\extensions\auth;

use \CComponent;
use \Yii;
use \common\components\FlagManager;
use \common\Config;

/**
 * BaseSSoClient
 */
abstract class BaseSsoClient extends CComponent{
	
	/**
	 * @var string ticket票据
	 */
	protected $_ticket;
	
	/**
	 * @var cache对象
	 */
	protected $_cache;
	
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
	 * @var array
	 */
	protected $_fields;
	
	//
	protected static $_propertys = array('email', 'mobile', 'truename', 'gender', 'birthday', 'password_question');
	
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
			$this->initTicketFlag();
		}
	}
	
	/**
	 * 获取cache对象
	 */
	public function getCacheComponent()
	{
		if ($this->_cache === NULL)
		{
			$conf = Config::get('Memcache');
			list($host, $port) = explode(':', $conf['default']);
			
			$config = array(
				'class' => 'CMemCache',
				'useMemcached' => extension_loaded('memcached'),
				'servers' => array(
					array(
						'host' => $host,
						'port' => $port,
						'weight' => 60,
					)
				),
				'keyPrefix' => 'veryeast_session_',
			);
			
			$this->_cache = Yii::createComponent($config);
			$this->_cache->init();
		}
		
		return $this->_cache;
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
		$cookie = $_COOKIE;
		return ($key == '') ? $cookie : (isset($cookie[$key]) ? $cookie[$key] : '');
	}
	
	/**
	 * 魔术方法 ，方便获取字段,$ssoClient->useid时可以直接获取userid字段
	 * @param string
	 * @return string
	 */
	public function __get($name)
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
				break;
		}
		
		if (in_array($name, self::$_propertys))
		{
			return $this->getField($name);
		}
		
		return parent::__get($name);
	}
	
	/**
	 * __set
	 */
	public function __set($name, $value)
	{
		if (in_array($name, self::$_propertys))
		{
			return $this->setField($name, $value);
		}
		return parent::__set($name, $value);
	}
}
