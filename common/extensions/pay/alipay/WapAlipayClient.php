<?php
/**
 * wap alipay类包
 *
 * @author     $Author: east.wu $
 * @version    $Rev: 1911 $
 * @date       $Date: 2014-04-14 10:43:02 +0800 (星期一, 14 四月 2014) $
 * @copyright  2003-2013 DFWSGROUP.COM
 * @link       http://tc.dfwsgroup.com/
 *
 */

namespace common\extensions\pay\alipay;
use \Yii;
use \AlipaySubmit;
use \AlipayNotify;

class WapAlipayClient
{
	
	//wap
	const WAP_TRADE_CREATE_SERVICE	= 'alipay.wap.trade.create.direct';
	
	const WAP_AUTH_SERVICE	= 'alipay.wap.auth.authAndExecute';
	
	//合作身份者id，以2088开头的16位纯数字
	public $partner;
	
	//安全检验码，以数字和字母组成的32位字符
	public $key;
	
	//卖家支付宝帐户
	public $seller_email;
	
	//字符编码格式 目前支持 gbk 或 utf-8
	public $input_charset	= 'utf-8';
	
	//签名方式 不需修改
	public $sign_type		= '0001';
	
	//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
	public $transport		= 'http';
	
	//默认的notifyUrl
	public $notifyUrl;
	
	//默认的returnUrl
	public $returnUrl;
	
	//操作中断返回地址
	public $merchantUrl;
	
	//默认的$showUrl
	public $showUrl;
	
	public $format = 'xml';
	
	public $v = '2.0';
	
	//ca证书路径地址，用于curl中ssl校验
	//请保证cacert.pem文件在当前文件夹目录中
	public $cacert;
	
	protected $_alipayConfig	= array();
	protected $_alipaySubmit;
	protected $_alipayNotify;
	
	function __construct()
	{
		$this->cacert		= __DIR__.'\\cacert.pem';
		$this->private_key_path = __DIR__ . '/key/rsa_private_key.pem';
		$this->ali_public_key_path = __DIR__ . '/key/alipay_public_key.pem';
		
		//将AlipaySubmit和AlipayNotify加入到Yii的classMap中以便调用时__autoload自动加载
		Yii::$classMap['AlipaySubmit'] = __DIR__.'/lib/wap/alipay_submit.class.php';
		Yii::$classMap['AlipayNotify'] = __DIR__.'/lib/wap/alipay_notify.class.php';
	}
	
	public function init()
	{
		$this->initAlipayConfig();
	}
	
	/**
	 * 初始化支付宝类的config配置
	 */
	function initAlipayConfig()
	{
		$this->_alipayConfig['partner']		= $this->partner;
		$this->_alipayConfig['key']			= $this->key;
		$this->_alipayConfig['input_charset']= $this->input_charset;
		$this->_alipayConfig['sign_type']	= $this->sign_type;
		$this->_alipayConfig['transport']	= $this->transport;
		$this->_alipayConfig['cacert']		= $this->cacert;
		$this->_alipayConfig['private_key_path']	= $this->private_key_path;
		$this->_alipayConfig['ali_public_key_path']	= $this->ali_public_key_path;
	}
	
	/**
	 * 获取AlipaySubmit对象
	 */
	function initAlipaySubmit()
	{
		$this->_alipaySubmit = new AlipaySubmit($this->_alipayConfig);
	}
	
	/**
	 * 获取AlipayNotify对象
	 */
	function initAlipayNotify()
	{
		$this->_alipayNotify = new AlipayNotify($this->_alipayConfig);
	}
	
	/**
	 * 初始化充值提交参数
	 * @param array $params
	 * @return array
	 */
	function initWapPayParam(array $params)
	{
		if ($this->_alipaySubmit === NULL)
		{
			$this->initAlipaySubmit();
		}
		
		$req_id = date('Ymdhis');
		
		//请求业务参数详细
		$req_data = '<direct_trade_create_req>';
		$req_data .= '<notify_url>' . $this->notifyUrl . '</notify_url>';
		$req_data .= '<call_back_url>' . $this->returnUrl . '</call_back_url>';
		$req_data .= '<seller_account_name>' . $this->seller_email . '</seller_account_name>';
		$req_data .= '<out_trade_no>' . $params['out_trade_no'] . '</out_trade_no><subject>' . $params['subject'] . '</subject>';
		$req_data .= '<total_fee>' . $params['total_fee'] . '</total_fee>';
		$req_data .= '<merchant_url>' . $this->merchantUrl . '</merchant_url>';
		$req_data .= '</direct_trade_create_req>';
		
		//构造要请求的参数数组，无需改动
		$para_token = array(
			"service" => self::WAP_TRADE_CREATE_SERVICE,
			"partner" => trim($this->partner),
			"sec_id" => trim($this->sign_type),
			"format"	=> $this->format,
			"v"	=> $this->v,
			"req_id"	=> $req_id,
			"req_data"	=> $req_data,
			"_input_charset"	=> trim(strtolower($this->input_charset))
		);
		
		//建立请求
		$html_text = $this->_alipaySubmit->buildRequestHttp($para_token);
		
		//URLDECODE返回的信息
		$html_text = urldecode($html_text);
		
		//解析远程模拟提交后返回的信息
		$para_html_text = $this->_alipaySubmit->parseResponse($html_text);
		
		//获取request_token
		$request_token = $para_html_text['request_token'];
		
		
		/**************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute**************************/
		
		//业务详细
		$req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
		//必填
		
		//构造要请求的参数数组，无需改动
		$parameter = array(
			"service"	=> "alipay.wap.auth.authAndExecute",
			"partner"	=> trim($this->partner),
			"sec_id"	=> trim($this->sign_type),
			"format"	=> $this->format,
			"v"			=> $this->v,
			"req_id"	=> $req_id,
			"req_data"	=> $req_data,
			"_input_charset"	=> trim(strtolower($this->input_charset))
		);
		
		return $parameter;
	}
	
	/**
	 * 返回充值表单html
	 *
	 * $alipayComponent->submitForm(array(
	 * 	'out_trade_no'	=> '1303280000000002',
	 * 	'subject'		=> '迈粒充值测试',
	 * 	'total_fee'		=> '0.01',
	 * 	'body'			=> '迈粒充值测试',
	 *
	 * 	//网银支付参数，目前没开通网银直连服务
	 * 	'paymethod'		=> 'bankPay',
	 * 	'defaultbank'	=>	''
	 * ));
	 * @param array $parameter
	 * @param string $method
	 * @param string $buttonName
	 */
	function wapSubmitForm(array $parameter = NULL, $method = 'get', $buttonName = '')
	{
		$parameter	= $this->initWapPayParam($parameter);
		$formHtml	= $this->alipaySubmitForm($parameter, $method, $buttonName);
		return $formHtml;
	}
	
	/**
	 * 返回充值表单html
	 * @param array $parameter
	 * @param string $method
	 * @param string $buttonName
	 * @return Ambigous <mixed, 提交表单HTML文本, string>
	 */
	function alipaySubmitForm(array $parameter, $method = 'post', $buttonName = '')
	{
		if ($this->_alipaySubmit === NULL)
		{
			$this->initAlipaySubmit();
		}
		$formHtml = $this->_alipaySubmit->buildRequestForm($parameter, $method, $buttonName);
		if (empty($buttonName))
		{
			$formHtml = str_replace("<input type='submit' value=''>", '', $formHtml);
		}
		return $formHtml;
	}
	
	/**
	 * 同步验证
	 * @return Ambigous <验证结果, boolean>
	 */
	function verifyReturn()
	{
		if ($this->_alipayNotify === NULL)
		{
			$this->initAlipayNotify();
		}
		
		return $this->_alipayNotify->verifyReturn();
	}
	
	/**
	 * 异步验证
	 * @return Ambigous <验证结果, boolean>
	 */
	function verifyNotify()
	{
		if ($this->_alipayNotify === NULL)
		{
			$this->initAlipayNotify();
		}
		return $this->_alipayNotify->verifyNotify();
	}
	
}