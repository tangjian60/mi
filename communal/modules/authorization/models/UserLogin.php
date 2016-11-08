<?php

namespace communal\modules\authorization\models;

use Yii;
use yii\base\Model;
use communal\helpers\HttpHelper;
use communal\helpers\ArHelper;
use yii\helpers\Json;

/**
 * User login Form
 */
class UserLogin extends Model
{
    public $username;
    public $password;
    public $password_type;
    public $return_type = 'json';
    public $unset_cookie = 1;
    public $encoding = 'utf-8';
    public $ip;
    public $captcha;
    public $store_login_time = 30;
    /* @var $from 来源：client, touch, pc, internal(内部) */
    public $from;
    public $ignore_key;

    //client need params
    public $platform = 1;
    public $device_token;

    public function init()
    {
        parent::init();
        $this->ip = Yii::$app->request->userIp;
        $this->ignore_key = md5('veryeast_client');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_login';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['username', 'password'], 'required'],
            [['username'], 'string', 'max' => 64],
            [['password'], 'string', 'max' => 32],
        ];

        if( $this->from != 'internal' ){
            $rules[] = ['captcha', 'required'];
            $rules[] = ['captcha', 'captcha', 'captchaAction' => '/authorization/client/captcha'];
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
            'captcha' => '验证码',
        ];
    }

    /**
     * Login
     * @return mixed
     */
    public function login()
    {
        $_ssoLoginServer	= 'http://sso.veryeast.cn/user/login';

        if( $this->from == 'internal' ){
            $this->password_type = 'md5';
        }

        $params = get_object_vars($this);
        $params = array_filter($params);

        $result = HttpHelper::post($_ssoLoginServer, $params);
        $data	= Json::decode($result);
        $data   = reset($data);

        if( $data['flag']==0 && $this->from ){//login success

            call_user_func([$this, $this->from . 'LoginSuccess'], $data);

        }

        return $data;
    }

    protected function _login(){
        //@todo 根据登录类型
        //@todo 判断用户是否可用
        //@todo 登陆成功后写cookie等操作
    }

    public function internalLoginSuccess($data)
    {
        //set cookie
        $expire = time() + 3600 * 24;
        $domain = '.veryeast.cn';

        setcookie('ticket', $data['ticket'], $expire, '/', $domain);
        setcookie('username', $data['username'], $expire, '/', $domain );
        setcookie('user_type', 2, $expire, '/', $domain);
    }

    /**
     * touch terminal after login
     * @param $data
     */
    public function touchLoginSuccess($data)
    {}

    /**
     * pc terminal after login
     * @param $data
     */
    public function pcLoginSuccess($data)
    {}

    /**
     * client terminal after login
     * @param $data
     */
    public function clientLoginSuccess($data)
    {
        $userid = $data['userid'];

        //设备码不为空时  保存设备码
        $this->device_token && $this->refreshDevice($userid, $this->device_token, $this->platform);

        $this->initPushSetting($userid);
    }

    //device bind
    public function refreshDevice($userid, $device_token, $platform)
    {
        //保存设备
        $deviceClass = ArHelper::className('ve_mobile.device');
        $device = $deviceClass::findOne(['device_type' => $platform, 'device' => $device_token]);
        if($device == null) {
            $device = new Device();
            $device->device_type = $platform;
            $device->device = $device_token;
            $device->save();
        }

        //更新用户与设备关系
        if($device && $device->id) {
            $class = ArHelper::className('ve_mobile.user_device_realtions');
            $class::deleteAll(['p_userid' => $userid, 'device_id' => $device->id]);
            $user_device_relation = $class::findOne(['p_userid' => $userid, 'device_id' => $device->id]);
            if($user_device_relation == null) {
                $user_device_relation = new $class();
                $user_device_relation->p_userid = $userid;
                $user_device_relation->device_id = $device->id;
            }
            $user_device_relation->modify_time = date('Y-m-d H:i:s');
            $user_device_relation->save();

            return $user_device_relation && $user_device_relation->id;
        }

        return false;
    }

    //device unbind
    public function unbindDevice($userid, $device_token, $platform)
    {
        $deviceClass = ArHelper::className('ve_mobile.device');
        $device = $deviceClass::findOne(['device_type' => $platform, 'device' => $device_token]);

        if( !empty($device) ){
            $class = ArHelper::className('ve_mobile.user_device_realtions');
            $class::deleteAll(['p_userid' => $userid, 'device_id' => $device->id]);
        }

        return true;
    }

    //init push setting
    public function initPushSetting($userid)
    {
        $class = ArHelper::className('ve_mobile.push_setting');
        $pushSetting = $class::findOne(['p_userid' => $userid]);
        if( !$pushSetting ){
            $pushSetting = new $class();
            $pushSetting->p_userid = $userid;
            $pushSetting->save();
        }

        return $pushSetting;
    }

}
