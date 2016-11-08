<?php
namespace common\extensions\push;
use \CComponent;

/**
 * @desc	iphone推送的接口程序
 */
class ApnsPush extends CComponent{

	//证书地址
	public $cert;
	
	//ipad证书地址
	public $ipadCert;
	
	//密钥
	public $passphrase;
	
	public $applePushUrl 	= 'ssl://gateway.push.apple.com:2195'; //ssl://gateway.sandbox.push.apple.com:2195测试地址
	public $defaultConf		= array(
								'badge' => 0,
								'sound' => 'default',
								'ispad' => 0,
								'alert' => '',
								'token' => ''
							);
	public $failedSend		= array();
	public $resendTime		= 0;
	public $maxResendTime	= 10;

	function init()
	{
		
	}
	
	/**
	 * 获取证书
	 * @param boolean $ispad
	 */
	function getCert($ispad = false)
	{
		return $ispad ? (empty($this->ipadCert) ? $this->cert : $this->ipadCert) : $this->cert;
	}
	
	function getPayload($data = array())
	{
		$body = array();
		strlen($data['alert']) > 87 && $data['alert'] = mb_convert_encoding(substr($data['alert'], 0, 87), 'UTF-8', 'UTF-8') . '...';
		$body['aps']['alert'] = $data['alert'];
		$body['aps']['badge'] = (int) $data['badge'];
		$body['aps']['sound'] = $data['sound'];
		$body = array_merge($body, array_diff_assoc(array_merge($data, $this->defaultConf), $this->defaultConf));
		return $body;
	}

	/**
	 * 推送
	 * @param array $sendInfo
	 * @return boolean
	 */
	function send($sendInfo = array())
	{
		$ctx = stream_context_create();
		$sendInfo = array_merge($this->defaultConf, $sendInfo);
		stream_context_set_option($ctx, 'ssl', 'local_cert', $this->getCert($sendInfo['ispad']));
		stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase); //如果设置了密码，这里就不能注释了
		$fp = stream_socket_client($this->applePushUrl, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
		if (!$fp)
		{
			return false;
		}
		$payload = json_encode($this->getPayload($sendInfo));
		//echo strlen($payload); //这里可以精心测试，最大不能超过256个字节即strlen超过256后苹果直接不予处理。
		$msg = chr(0) . pack("n", 32) . pack('H*', str_replace(' ', '', $sendInfo['token'])) . pack("n", strlen($payload)) . $payload;
		fwrite($fp, $msg);
		fclose($fp);
		return true;
	}
	
	/**
	 * 批量推送
	 * @param array $batchSendInfo
	 */
	function batchSend($batchSendInfo = array())
	{
		$this->failedSend = array();
		if( ! empty($batchSendInfo))
		{
			foreach ($batchSendInfo as $val)
			{
				if( ! $this->send($val))
				{
					$this->failedSend[] = $val;
				}
			}
			if( ! empty($this->failedSend) && $this->resendTime < $this->maxResendTime )
			{
				$this->resendTime++;
				$this->batchSend($this->failedSend);
			}
		}
	}
}