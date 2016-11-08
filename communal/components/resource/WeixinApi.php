<?php

namespace communal\components\resource;

use Yii;
use yii\base\InvalidConfigException;
use communal\components\BaseComponent;
use communal\extensions\utils\Curl;

/**
 * weixin Api
 * example: 
 * $api = Yii::getCommunalComponent('weixinApi', ['account' => 'veryeast']);
 * $api->accessToken //得到access_token
 * $api->openid //得到用户openid
 * ----------
 * @desc 获取opendid、授权、获取access_token、获取jsticket、获取分享配置
 * @author xing wang
 */
class WeixinApi extends BaseComponent
{
    //mem key
    protected $prefixKey = 'communal\\components\\resource\\Weixin';

    public $account;
    private $appid;
    private $appsecret;

    public function init()
    {
    	parent::init();
    	$config = Yii::getConfig($this->account, '@communal/config/weixin/account.php');

    	if( empty($config) )
    		throw new InvalidConfigException('weixin account error!');

    	$this->appid = $config['appid'];
    	$this->appsecret = $config['appsecret'];
    }

    //获取Openid
    public function getOpenid()
    {
        //通过code获得openid
        if (!isset($_GET['code'])){
                
            $this->getCode('snsapi_base');
            
        } else {
            //获取code码，以获取openid
            $code = $_GET['code'];

            $urlObj["appid"] = $this->appid;
            $urlObj["secret"] = $this->appsecret;
            $urlObj["code"] = $code;
            $urlObj["grant_type"] = "authorization_code";
            $bizString = $this->ToUrlParams($urlObj);
            $url = "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;

            $data = Curl::curl_get_content($url);
            //取出openid
            $data = json_decode($data,true);
            $openid = $data['openid'];
            return $openid;
        }
    }

    //网页授权 获取用户信息
    public function getSnsUserInfo()
    {
        if (!isset($_GET['code'])){
            
            $this->getCode('snsapi_userinfo');

        } else {
            //获取code码，以获取 access_token
            $params = [
                'grant_type' => 'authorization_code',
                'appid'      => $this->appid,
                'secret'     => $this->appsecret,
                'code'       => $code
            ];

            $bizString = $this->ToUrlParams($params);
            $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . $bizString;
            $data = Curl::curl_get_content($url);

            $data = json_decode($data, true);

            //通过access_token ,获取用户信息
            if( isset($data['access_token']) ){

                $params = [
                    'access_token' => $data['access_token'],
                    'openid'      => $data['openid'],
                    'lang' => 'zh_CN'     
                ];

                $string = $this->ToUrlParams($params);
                $url = 'https://api.weixin.qq.com/sns/userinfo?' . $string;
                $data = Curl::curl_get_content($url);

                return json_decode($data, 1);
            }
        }

        return false;
    }

    /**
     * 获取access_token,唯一调用地址
     *
     * @param  string $appId
     * @param  string $appSecrect
     * @return [type]       [description]
     */
   	public function getAccessToken()
    {
        $cacheKey = $this->appid . '_access_token';
        $access_token = parent::getCache( $cacheKey );

        if(! $access_token){
        	$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
	        $data = Curl::curl_get_content($url);
	        $data = json_decode($data, true);
	        
	        if( $data && !empty($data['access_token']) ){
	        	$access_token = $data['access_token'];
	            $expires_in = $data['expires_in'] - 600; 
	            parent::setCache($cacheKey, $access_token, $expires_in);
	        }

        }

        return $access_token;
    }

    /**
     * 获取jsticket
     *
     * @param  string $appid     [description]
     * @param  string $appsecret [description]
     * @return [type]            [description]
     */
    public function getJsApiTicket($appid = '', $appsecret = '')
    {

	    $cacheKey = $this->appid . '_jsapiticket';
	    $jsApiTicket = parent::getCache( $cacheKey );

	    if( !$jsApiTicket ){
	    	$access_token = $this->accessToken;
	    	$url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token='.$access_token;
       		$data = Curl::curl_get_content($url);
        	$data = json_decode($data, true);
        	if( $data && !empty($data['ticket']) ){
        		$jsApiTicket = $data['ticket'];
	            $expires_in = $data['expires_in'] - 600; 
	            parent::setCache($cacheKey, $jsApiTicket, $expires_in);
            }
	    }

	    return $jsApiTicket;
    }

    /**
     * 获取微信wx.config({...})配置
     *
     * @param  string $appid     [description]
     * @param  string $appsecret [description]
     * @return [type]            [description]
     */
    public function getSignPackage()
    {
        $jsapiTicket = $this->jsApiTicket;
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "{$protocol}{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);
        $signPackage = array(
          "appId"     => $this->appid,
          "nonceStr"  => $nonceStr,
          "timestamp" => $timestamp,
          "url"       => $url,
          "signature" => $signature,
          "rawString" => $string
        );
        return $signPackage;
    }

    //触发微信返回code码
    private function getCode($scope = 'snsapi_base')
    {
        $redirectUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING']);

        $params = [
            'appid' => $this->appid,
            'redirect_uri' => "$redirectUrl",
            'response_type' => "code",
            'scope' => $scope,
            'state' => "STATE"."#wechat_redirect",
        ];
            
        $bizString = $this->ToUrlParams($params);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;

        Header("Location: $url");
        exit();
    }

    /**
     * 
     * 拼接签名字符串
     * @param array $urlObj
     * 
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }
        
        $buff = trim($buff, "&");
        return $buff;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    
}