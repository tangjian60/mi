<?php
namespace common\extensions\auth;
use common\models\VEUser;
use common\components\FlagManager;
use common\models\mssql_jdrc\Users as MssqlUser;
/**
 * SsoClient
 * 基于ticket的用户操作，继承了SoapSsoClient，重写了常用的几个方法改为直接连接数据库查询
 *
 */
class SsoClient extends SoapSsoClient{
	
	protected $_sessionData;
	
	protected $_isNeedSaveSession	= FALSE;
	
	public $useCache = true;
	
	public function init($ticket = '')
	{
		parent::init($ticket);
		
		if ($this->useCache)
		{
			\Yii::app()->onEndRequest = array($this, 'saveSession');
		}
	}
	
	/**
	 * 初始化ticket的flag
	 *
	 * @return void
	 */
	public function initTicketFlag()
	{
		if (empty($this->_ticket))
		{
			$this->_ticketFlag = FlagManager::FLAG_TICKET_EMPTY;
			$this->_userid = 0;
		}
		else
		{
			$userid = $this->getId();
			if ($userid == 0)
			{
				$this->_ticketFlag = FlagManager::FLAG_TICKET_IS_NOT_VALID;
			}
			else
			{
				$this->_ticketFlag = FlagManager::FLAG_TICKET_IS_VALID;
			}
		}
	}
	
	/**
	 * 获取用户userid
	 *
	 * @return userid
	 */
	public function getId()
	{
		if ($this->_userid !== NULL)
		{
			return $this->_userid;
		}
		
		$this->_userid = VEUser::callFunction('f_get_id', array('user', 'ticket', $this->_ticket));
		
		return $this->_userid;
	}
	
	/**
	 * 设置用户userid
	 * @param intval $value
	 */
	public function setId($value)
	{
		$id = intval($value);
		if ($id > 0)
		{
			$this->_userid = $id;
			$this->_ticketFlag = FlagManager::FLAG_TICKET_IS_VALID;
		}
	}
	
	/**
	 * 获取用户类型userType 
	 *
	 * @return userType
	 */
	public function getUserType()
	{
		if ( ! $this->_userid)
		{
			return NULL;
		}
		$userType = $this->getSession('userType');
		if ($userType == NULL)
		{
			$userType = VEUser::callFunction('f_get_field', array($this->_userid, 'user', 'user_type'));
			if ($userType)
			{
				$this->setSession('userType', $userType);
			}
		}
		return $userType;
	}
	
	/**
	 * 获取用户名username
	 *
	 * @return username
	 */
	public function getUsername()
	{
		if ( ! $this->_userid)
		{
			return NULL;
		}
		
		$username = $this->getSession('username');
		
		if ($username === NULL)
		{
			$username = VEUser::callFunction('f_get_field', array($this->_userid, 'user', 'username'));
			if ($username)
			{
				$this->setSession('username', $username);
			}
		}
		return $username;
	}
	
	/**
	 * 获取用户信息
	 * @example $this->getField('email');	=>	'aa@aa.com';
	 *
	 * @param string 用户字段  email,mobile,truename,gender,birthday,password_question等
	 * @return string 字段值
	 */
	public function getField($column)
	{
		if ($this->_ticketFlag == FlagManager::FLAG_TICKET_IS_VALID)
		{
			/* $value = $this->getSession($column);
			if ($value === NULL)
			{
				$value = VEUser::callFunction('f_get_field', array($this->_userid, 'user', $column));
				$this->setSession($column, $value);
			} */
			
			$value = VEUser::callFunction('f_get_field', array($this->_userid, 'user', $column));
			return $value;
		}
	}
	
	/**
	 * 设置用户信息
	 * @return string 返回码flag 0表示执行成功
	 */
	public function setField($column, $value = '', $resupply = '')
	{
		switch ($column)
		{
			case 'email':
				$userType = $this->getUserType();
				if ($userType == 2)
				{
					if (VEUser::callFunction('f_is_exist_field', array('user', 'email2', $value)) == FlagManager::FLAG_EMAIL_EXISTS)
					{
						$res = FlagManager::FLAG_EMAIL_EXISTS;
					}
					else
					{
						$res = VEUser::callFunction('f_set_field', array($this->_userid, 'user', 'email', $value));
					}
				}
				else
				{
					$res = VEUser::callFunction('f_set_field', array($this->_userid, 'user', 'email', $value));
				}
				break;
			case 'mobile':
			case 'truename':
			case 'gender':
			case 'birthday':
				$res = VEUser::callFunction('f_set_field', array($this->_userid, 'user', $column, $value));
				break;
		}
		
		if ($res == FlagManager::FLAG_SUCCESS)
		{
			$this->setSession($column, $value);
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
		if ( ! $this->_userid)
		{
			return FlagManager::FLAG_TICKET_IS_NOT_VALID;
		}
		
		$password = VEUser::callFunction('f_get_field', array($this->_userid, 'user', 'password'));
		if ( ! $this->verifyPassword($oldPassword))
		{
			return FlagManager::FLAG_PASSWORD_ERROR;
		}
		
		$res = VEUser::callFunction( 'f_set_field', array($this->_userid, 'user', 'password', md5($newPassword)));
		
		VEUser::callFunction('f_delete_ticket', array('userid', $this->_userid));
		//MssqlUser::model()->updateByPK($this->_userid, array('Md5Password', substr(md5($newPassword), 8, 16)));
		
		return $res;
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
		if ( ! $this->_userid)
			return FlagManager::FLAG_TICKET_IS_NOT_VALID;
		
		if ( ! $this->verifyPassword($password))
			return FlagManager::FLAG_PASSWORD_ERROR;
		
		VEUser::callFunction('f_set_field', array($this->_userid, 'user', 'password_question', $question));
		VEUser::callFunction('f_set_field', array($this->_userid, 'user', 'password_answer', $answer));
		
		return FlagManager::FLAG_SUCCESS;
	}
	
	/**
	 * 验证密码
	 */
	protected function verifyPassword($enterPassword)
	{
		$enterPassword = md5($enterPassword);
		$truePassword = VEUser::callFunction('f_get_field', array($this->_userid, 'user', 'password'));
		(strlen($truePassword) == 16) && $enterPassword = substr($enterPassword, 8, 16);
		 
		return  ($enterPassword == $truePassword) ? TRUE : FALSE;
	}
	
	
	/**
	 * 获取session
	 */
	public function getSession($key = NULL)
	{		
		if ($this->_sessionData === NULL && $this->useCache)
		{
			$userid = $this->getId();
			$cacheComponent = $this->getCacheComponent();
			$sessionData = $cacheComponent->get($userid);
			$this->_sessionData = ! empty($sessionData) ? $sessionData : array();
		}
		
		if (empty($key))
		{
			return $this->_sessionData;
		}
		
		if (isset($this->_sessionData[$key]))
		{
			if ( isset($this->_sessionData[$key][1]))
			{
				//session过期
				if ($this->_sessionData[$key][1] < time())
				{
					$this->deleteSession($key);
					return NULL;
				}
			}
			return isset($this->_sessionData[$key][0]) ? $this->_sessionData[$key][0] : NULL;
		}
		return NULL;
	}
	
	/**
	 * 设置Session
	 */
	public function setSession($key, $value, $expire = NULL)
	{
		
		$this->_isNeedSaveSession = TRUE;
		
		$sessionData = $this->getSession();
		
		$data = array($value);
		
		if ( ! empty($expire) && $expire > 0)
		{
			$data[] = time() + $expire;
		}
		
		$this->_sessionData[$key] = $data;
		
		return TRUE;
	}
	
	/**
	 * 删除session
	 */
	public function deleteSession($key)
	{
		$this->_isNeedSaveSession = TRUE;
		
		if (isset($this->_sessionData[$key]))
		{
			unset($this->_sessionData[$key]);
		}
	}
	
	/**
	 * 清空session
	 */
	public function clearSession()
	{
		$this->_isNeedSaveSession = TRUE;
		$this->_sessionData = array();
	}
	
	/**
	 * 清除有有效期的session
	 */
	public function clearExpireSession()
	{
		if ( ! empty($this->session))
		{
			$this->_sessionData = array();
		}
		
		$this->saveSessionData();
	}
	
	/**
	 * 保存session数据事件,程序结束前触发
	 * @param object $event
	 */
	public function saveSession($event)
	{
		if ($this->_isNeedSaveSession)
		{
			return $this->saveSessionData();
		}
	}
	
	/**
	 * 保存session数据
	 */
	public function saveSessionData()
	{
		$userid = $this->getId();
		if ($userid > 0)
		{
			$cacheComponent = $this->getCacheComponent();
			if (empty($this->_sessionData))
			{
				$cacheComponent->delete($userid);
			}
			else
			{
				return $cacheComponent->set($userid, $this->_sessionData);
			}
		}
	}
}
