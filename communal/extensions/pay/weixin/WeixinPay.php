<?php
/**
 * 微信  支付
 *
 * @author     
 * @version    1.0.0
 * @date       2016-04-07
 * @copyright  2003-2015 DFWSGROUP.COM
 * @link       http://tc.dfwsgroup.com/
 *
 */

namespace communal\extensions\pay\weixin;

use Yii;
use WxPayUnifiedOrder;
use JsApiPay;
use WxPayApi;

require_once __DIR__ . '/lib/WxPay.Api.php';
require_once __DIR__ . '/lib/WxPay.JsApiPay.php';

class WeixinPay{

	//商户支付密钥，参考开户邮件设置（必须配置）
	public $key;
	
	//公众帐号secert（仅JSAPI支付的时候需要配置）
	public $appsecret;
	
	//绑定支付的APPID（必须配置）
	public $appid;
	
	//商户号（必须配置）
	public $mch_id;
	
	//签名
	public $sign;
	
	//字符编码格式 目前支持 gbk 或 utf-8
	public $input_charset	= 'utf-8';
	
	//签名方式 不需修改
	public $sign_type		= 'MD5';
	
	//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
	public $transport		= 'https';
	
	//证书路径地址，用于curl中ssl校验
	private $sslcert;
	private $sslkey;
	
	//默认的notifyUrl
	public $returnUrl = '/pay/respond/wxpayReturn';
	
	//统一接口请求地址
	const UNI_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
	
	function __construct(){
		$this->sslcert =  Yii::getAlias('@communal/config/weixin/weixin_pay_apiclient_cert.pem');
		$this->sslkey  =  Yii::getAlias('@communal/config/weixin/weixin_pay_apiclient_key.pem');

		$config = Yii::getConfig('weixin', '@communal/modules/pay/config/pay_config.php');
		$this->appid 	 = $config['appid'];
		$this->mch_id 	 = $config['mch_id'];
		$this->key  	 = $config['key'];
		$this->appsecret = $config['appsecret'];
		$this->returnUrl = $config['returnUrl'];
	}

	public function init(){}
		
	
	/**
	 * 初始化充值提交参数
	 * @param array $params [openid,body,out_trade_no,total_fee]
	 * @return array
	 */
	function initWxpayParams(array $params){

		//统一下单
		$input = new WxPayUnifiedOrder();
		$input->SetBody($params['body']);
		//$input->SetAttach("test");
		$input->SetOut_trade_no($params['out_trade_no']);
		$input->SetTotal_fee($params['total_fee']);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		//$input->SetGoods_tag("test");
		$input->SetNotify_url($this->returnUrl);
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($params['openid']);

		$input->SetAppid($this->appid);//公众账号ID
		$input->SetMch_id($this->mch_id);//商户号

		$order = WxPayApi::unifiedOrder($input);
		
		$tools = new JsApiPay();
		$jsApiParameters = $tools->GetJsApiParameters($order);

		return $jsApiParameters;
	}

}