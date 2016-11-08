<?php

namespace communal\components\auth;

use Yii;
use communal\models\VeUser;
use yii\base\InvalidParamException;
use yii\base\InvalidCallException;
use communal\helpers\EncryptionHelper;

/**
 * 最佳东方用户 ： ve_user.user_login  
 * User Component
 */
class User extends \yii\web\User implements \ArrayAccess
{
	public $enableAutoLogin = true;
	public $autoRenewCookie = false;

	protected function loginByCookie()
	{
        //ticket from mobile
        $user_ticket = Yii::$app->request->post('user_ticket') ? : Yii::$app->request->get('user_ticket');
		$ticket = isset($_COOKIE['ticket']) ? $_COOKIE['ticket'] : ($user_ticket ? $user_ticket : null);
        if ($ticket === null) {
            return;
        }

        $res = EncryptionHelper::authcode( $ticket, 'DECODE' );
		
		if($res){
			$ticket_info = explode('\t', $res);
			if( ! empty($ticket_info) )
			{
				$id = VeUser::callFunction('f_get_id', array( 'user', 'ticket', $ticket ));
				if( $ticket_info[0] == $id ) 
				{	
					$class = $this->identityClass;
			        $identity = $class::findIdentity($id);
			        if ($identity === null) {
			            return;
			        } elseif (!$identity instanceof \yii\web\IdentityInterface) {
			            throw new \yii\base\InvalidValueException("$class::findIdentity() must return an object implementing IdentityInterface.");
			        }

			        $duration = 0;
		            if ($this->beforeLogin($identity, true, $duration)) {

		                $this->switchIdentity($identity, $this->autoRenewCookie ? $duration : 0);
		                $ip = Yii::$app->getRequest()->getUserIP();
		                Yii::info("User '$id' logged in from $ip via cookie.", __METHOD__);
		                $this->afterLogin($identity, true, $duration);

		            }
				}
			}
		}
	}

	public function offsetExists($index) 
    {
        $params = explode('.', $index);

        $next = function($key, $obj){
            try{
                return $obj->$key;
            }catch(\Exception $e){
                return false;
            }
        };

        $result = $this->identity;
        foreach($params as $param){
            $result = $next($param, $result);
        }

        return $result === false ? false : true;
    }
    
    /**
     * 获取用户相关的字段 或 model object
     * Example:
     * 1) get ve_user.user_login  的  username字段:  Yii::$app->user['username']
     * 2) get ve_user.user_info Model Object: Yii::$app->user['userInfo']
     * 3) get ve_user.user_info 的 email: Yii::$app->user['userInfo.email']
     *
     * @see commumal\models\ve_user\UserLogin.php
     * @param string index
     */
    public function offsetGet($index) 
    {
        $params = explode('.', $index);

        $next = function($key, $obj){
            return $obj->$key;
        };

        $result = $this->identity;
        foreach($params as $param){
            $result = $next($param, $result);
        }
        
        return $result;
    }
    
    public function offsetSet($index, $value) 
    {
        throw new InvalidCallException('Can not set a property in this class');
    }
    
    public function offsetUnset($index) 
    {
        throw new InvalidCallException('Can not unset a property in  this class');
    }
	
}
