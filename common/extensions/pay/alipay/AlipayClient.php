<?php
/**
 * web alipay类包
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

class AlipayClient
{
	//直接支付接口接入service
	const DIRECT_PAY_SERVICE			= 'create_direct_pay_by_user';
	
	//即时到账批量退款有密接口接入service
	const FASTPAY_REFUND_SERVICE		= 'refund_fastpay_by_platform_pwd';
	
	//担保交易接口接入service
	const PARTNER_TRADE_SERVICE			= 'create_partner_trade_by_buyer';
	
	//确认发货接口接入service
	const GOODS_CONFIRM_SERVICE			= 'send_goods_confirm_by_platform';
	
	//快捷登录接口接入service
	const QUICK_LOGIN_SERVICE			= 'user.auth.quick.login';
	
	//批量付款到支付宝账户有密接口接入service
	const BATCH_TRANS_NOTIFY_SERVICE	= 'batch_trans_notify';
	
	//标准双接口接入service
	const TRADE_CREATE_BY_BUYER_SERVICE	= 'trade_create_by_buyer';
	
	//单笔交易查询接口single_trade_query
	const SINGLE_TRADE_QUERY_SERVICE	= 'single_trade_query';
	
	//默认的payment_type
	const DEFAULT_PAYMETN_TYPE	= 1;
	
	//合作身份者id，以2088开头的16位纯数字
	public $partner;
	
	//安全检验码，以数字和字母组成的32位字符
	public $key;
	
	//卖家支付宝帐户
	public $seller_email;
	
	//字符编码格式 目前支持 gbk 或 utf-8
	public $input_charset	= 'utf-8';
	
	//签名方式 不需修改
	public $sign_type		= 'MD5';
	
	//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
	public $transport		= 'http';
	
	//默认的notifyUrl
	public $notifyUrl;
	
	//默认的returnUrl
	public $returnUrl;
	
	//默认退款返回url
	public $refundNotifyUrl;
	
	//默认的$showUrl
	public $showUrl;
	
	//ca证书路径地址，用于curl中ssl校验
	//请保证cacert.pem文件在当前文件夹目录中
	public $cacert;
	
	protected $_alipayConfig	= array();
	protected $_alipaySubmit;
	protected $_alipayNotify;
	
	function __construct($partner = '', $key = '', $seller_email = '', $input_charset = 'utf-8', $sign_type = 'MD5', $transport = 'http')
	{
		$this->partner		= $partner;
		$this->key			= $key;
		$this->seller_email = $seller_email;
		$this->input_charset= strtolower($input_charset);
		$this->sign_type	= strtoupper($sign_type);
		$this->transport	= $transport;
		$this->cacert		= __DIR__.'\\cacert.pem';
		$this->initAlipayConfig();
		
		//将AlipaySubmit和AlipayNotify加入到Yii的classMap中以便调用时__autoload自动加载
		Yii::$classMap['AlipaySubmit'] = __DIR__.'/lib/alipay_submit.class.php';
		Yii::$classMap['AlipayNotify'] = __DIR__.'/lib/alipay_notify.class.php';
	}
	
	/**
	 * 初始化参数
	 * @param array $params
	 */
	function init(array $params = array())
	{
		if ( ! empty($params))
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
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
	function initDirectPayParam(array $params)
	{
		if ($this->_alipaySubmit === NULL)
		{
			$this->initAlipaySubmit();
		}
		
		$default = array(
					'service'		=> self::DIRECT_PAY_SERVICE,
					'partner'		=> $this->partner,
					'payment_type'	=> self::DEFAULT_PAYMETN_TYPE,
					'notify_url'	=> $this->notifyUrl,
					'return_url'	=> $this->returnUrl,
					'seller_email'	=> $this->seller_email,
					'out_trade_no'	=> '',
					'subject'		=> '',
					'total_fee'		=> '',
					'body'			=> '',
					'show_url'		=> $this->showUrl,
					'anti_phishing_key'	=> $this->_alipaySubmit->query_timestamp(),
					'exter_invoke_ip'	=> Yii::app()->request->userHostAddress,
					'_input_charset'	=> $this->input_charset
				);
		return array_merge($default, $params);
	}
	
	/**
	 * 初始化退款提交参数
	 * @param array $params
	 * @return array
	 */
	function initRefundParam(array $params)
	{
		if ($this->_alipaySubmit === NULL)
		{
			$this->initAlipaySubmit();
		}
		
		$default = array(
					'service'		=> self::FASTPAY_REFUND_SERVICE,
					'partner'		=> $this->partner,
					'seller_email'	=> $this->seller_email,
					'notify_url'	=> $this->refundNotifyUrl,
					'refund_date'	=> date('Y-m-d H:i:s'),
					'batch_no'		=> '',
					'batch_num'		=> '',
					'detail_data'	=> '',
					'_input_charset'=> $this->input_charset
				);
		return array_merge($default, $params);
	}
	
	/**
	 * 初始化单笔交易查询参数
	 * @param array $params
	 * @return array
	 */
	function initQueryParam(array $params)
	{
		if ($this->_alipaySubmit === NULL)
		{
			$this->initAlipaySubmit();
		}
		
		$default = array(
					'service'		=> self::SINGLE_TRADE_QUERY_SERVICE,
					'partner'		=> $this->partner,
					'trade_no'		=> '',
					'out_trade_no'	=> '',
					'_input_charset'=> $this->input_charset
				);
		return array_merge($default, $params);
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
	function submitForm(array $parameter = NULL, $method = 'post', $buttonName = '')
	{    
		$parameter	= $this->initDirectPayParam($parameter);
		$formHtml	= $this->alipaySubmitForm($parameter, $method, $buttonName);
		return $formHtml;
	}
	
	/**
	 * 返回退款表单html
	 * 
	 * $alipayComponent->submitRefundForm(array(
	 * 	'batch_no'	=> '201304010001',	//批次号，必填，格式：当天日期[8位]+序列号[3至24位]，如：201304010001
	 * 	'batch_num'	=> '1',	//退款笔数
	 * 	'detail_data'=> '2013032860804388^0.01^test'	//多笔为#相隔 2013032860804388^0.01^test#2013032860804389^0.01^test
	 * ));
	 * @param array $parameter
	 * @param string $method
	 * @param string $buttonName
	 */
	function submitRefundForm(array $parameter = NULL, $method = 'post', $buttonName = '')
	{
		$parameter	= $this->initRefundParam($parameter);
		$formHtml	= $this->alipaySubmitForm($parameter, $method, $buttonName);
		return $formHtml;
	}
	
	/**
	 * 单笔交易查询
	 * 目前暂未开通该功能的服务，未测试过
	 * @param array $parameter
	 * @param string $method
	 * @param string $buttonName
	 */
	function tradeQuery(array $parameter = NULL)
	{
		$parameter	= $this->initQueryParam($parameter);
		$content	= $this->_alipaySubmit->buildRequestHttp($parameter);
		return $content;
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