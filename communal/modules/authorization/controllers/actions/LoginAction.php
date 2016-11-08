<?php

namespace communal\modules\authorization\controllers\actions;

use Yii;
use yii\base\Action;
use communal\helpers\ArHelper;
use communal\modules\authorization\models\UserLogin;
use yii\base\InvalidCallException;

//登陆
class LoginAction extends Action
{
    use \communal\components\OutputTrait;

    /* @var $from 登录端：client, touch, pc */
    public $from = 'client';
    
    public function run()
    {
        $callable =  [$this, $this->from . 'Login'];
        if( is_callable($callable) )
            call_user_func($callable);
        else
            throw new InvalidCallException('bad request!');

    }

    protected function clientLogin(){

        $model = new UserLogin();
        $model->load(Yii::$app->request->post(), '');
        $model->from = $this->from;

        if( !$model->validate() ){

            $this->ajaxError(-1, implode(',', $model->firstErrors ) );

        }

        $result = $model->login();

        if ($result['flag'] == 1011 || $result['flag'] == 1033)
            $this->ajaxError(-1, '用户名与密码不匹配，登录失败！');

        if ($result['user_type'] == 1)
            $this->ajaxError(-1, '请使用个人用户登录！');

        //登陆成功
        if ( $result['flag'] == 0 ) {

            $email = ArHelper::field('ve_user.user_info.email',  $result['userid']);
            $phone = ArHelper::field('ve_user.user_login.bind_mobile',  $result['userid']);

            $this->ajaxOutput([
                'user_ticket' => $result['ticket'],
                'user_id' =>  $result['userid'],
                'user_name' => $result['username'],
                'email' => $email,
                'phone' => $phone
            ], '登录成功，欢迎您！');

        }

        $this->ajaxError(-1, '登陆失败');
    }

    protected function touchLogin(){}

    protected function pcLogin(){}

}
