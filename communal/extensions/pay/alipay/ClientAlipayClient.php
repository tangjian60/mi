<?php
/**
 * 手机客户端 alipay类包
 *
 * @author     $Author: east.wu $
 * @version    $Rev: 1911 $
 * @date       $Date: 2014-04-14 10:43:02 +0800 (星期一, 14 四月 2014) $
 * @copyright  2003-2013 DFWSGROUP.COM
 * @link       http://tc.dfwsgroup.com/
 *
 */

namespace communal\extensions\pay\alipay;
use \Yii;
use \AlipaySubmit;
use \AlipayNotify;

class ClientAlipayClient
{
    
    const MOBILE_PAY_SERVICE	= 'mobile.securitypay.pay';
    
    //合作身份者id，以2088开头的16位纯数字
    public $partner;
    
    //安全检验码，以数字和字母组成的32位字符
    public $key;
    
    //卖家支付宝帐户
    public $seller_email;
    
    //字符编码格式 目前支持 gbk 或 utf-8
    public $input_charset	= 'utf-8';
    
    //签名方式 不需修改
    public $sign_type		= 'RSA';
    
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
    
    public $itBPay = '30m';
    
    public $paymentType = 1;
    
    
    //ca证书路径地址，用于curl中ssl校验
    //请保证cacert.pem文件在当前文件夹目录中
    public $cacert;
    
    protected $_alipayConfig	= array();
    protected $_alipaySubmit;
    protected $_alipayNotify;
    
    function __construct()
    {
        $this->cacert		= __DIR__.'/cacert.pem';
        $this->private_key_path = __DIR__ . '/key/rsa_private_key.pem';
        $this->ali_public_key_path = __DIR__ . '/key/client_public_key.pem';
    
        Yii::$classMap['AlipaySubmit'] = __DIR__.'/lib/client/alipay_submit.class.php';
        Yii::$classMap['AlipayNotify'] = __DIR__.'/lib/client/alipay_notify.class.php';
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
    function initClientPayParam(array $params)
    {
        $parameter = array(
            "partner"	=> trim($this->partner),
            "seller_id" => $this->seller_email,
            "out_trade_no"=> $params['out_trade_no'],
            "subject"   => $params['subject'],
            "body"      => $params['body'],
            "total_fee" => $params['total_fee'],
            "notify_url"=> $this->notifyUrl,
            "service"	=> self::MOBILE_PAY_SERVICE,
            "payment_type"    =>$this->paymentType,
            "_input_charset"  => trim(strtolower($this->input_charset)),
            "it_b_pay"  =>$this->itBPay,
            "show_url"  => $this->showUrl
        );
    
        return $parameter;
    }
    
    /**
     * 
     */
    public function clientSubmitParam($params)
    {
        if ($this->_alipaySubmit === NULL)
        {
            $this->initAlipaySubmit();
        }
        
        $data = $this->initClientPayParam($params);
        $res = $this->_alipaySubmit->buildRequestPara($data);
        
        return $res;
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