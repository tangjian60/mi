<?php
namespace common\extensions\auth;
use common\models\VEUser;
use common\components\FlagManager;
/**
 * SsoClient
 * 基于ticket的用户操作，继承了SoapSsoClient，重写了常用的几个方法改为直接连接数据库查询
 *
 */
class SsoClient extends SoapSsoClient{
	
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
			$this->_ticketFlag = VEUser::callFunction('f_is_exist_field', array('ticket', 'ticket_id', $this->_ticket));
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
		$this->_userid = VEUser::callFunction('f_get_id', array('user', 'ticket', $this->_ticket));
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
		
		if ($this->_userid > 0)
		{
			$this->_userType = VEUser::callFunction('f_get_field', array($this->_userid, 'user', 'user_type'));
		}
		else
		{
			$this->_userType = VEUser::callFunction('f_get_field', array($this->_ticket, 'ticket', 'user_type'));
		}
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
		
		if ($this->_userid > 0)
		{
			$this->_username = VEUser::callFunction('f_get_field', array($this->_userid, 'user', 'username'));
		}
		else
		{
			$this->_username = VEUser::callFunction('f_get_field', array($this->_ticket, 'ticket', 'username'));
		}
		return $this->_username;
	}
	
}
