<?php
namespace common\extensions\sms;
use common\models\MssqlJdrc;


/**
 * Class SmsApi
 * @package common\extensions\sms
 * @author niupeiyuan
 * @date 2013-05-21
 */
class SmsApi
{
	protected $_connection = null;
	protected $_tableName = 'dbo.SMS_Send';
	protected $_account = 'zjdf';
	protected $_pwd = 'zjdf^123456';

	/**
	 *获取dbconection
	 */
	public function __construct()
	{

		$this->_connection = MssqlJdrc::getDb();
	}

	/** 插入短息队列
	 * @param string $mobile 手机号码
	 * @param string $content 短信内容
	 * @return bool 是否发送成功
	 * @throws Exception 参数错误
	 */
	public function sendSms($mobile = '', $content = '')
	{
		if (!$mobile || !$content) {
			throw new Exception('param is error');
		}

		$result = $this->_connection->createCommand()->insert(
			$this->_tableName,
			array('Mobile' => $mobile, 'Content' => $content, 'Account' => $this->_account, 'PWD' => $this->_pwd)
		);
		
		return $result ? TRUE : FALSE;
	}


	/**
	 *
	 */
	public function __destruct()
	{
		unset($this->_connection);
	}
}
