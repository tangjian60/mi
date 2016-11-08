<?php
namespace communal\components\auth;

use communal\models\ve_user\UserLogin;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use communal\models\ve_user\UserInfo;
use communal\models\ve_person\Person;
use communal\models\ve_company\Company;

/**
 * 用户认证
 */
class UserIdentity extends \communal\models\VeUser implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{user_login}}';
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['userid' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        //$this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function getUserInfo()
    {
        return $this->hasOne(UserInfo::className(), ['userid' => 'userid']);
    }

    public function getUserAccountInfo()
    {
        return $this->hasOne(UserAccountInfo::className(), ['userid' => 'userid']);
    }

    public function getPerson()
    {
        if($this->user_type == 2)
            return $this->hasOne(Person::className(), ['user_id' => 'userid']);
        else 
            return null;
    }

    public function getCompany()
    {
        if($this->user_type == 1)
            return $this->hasOne(Company::className(), ['c_userid' => 'userid']);
        else 
            return null;
    }

    //是否验证手机
    public function getIsVerifyMobile()
    {
        $info = $this->userInfo;
        if($this->bind_mobile){
            if(!$info->is_verify_mobile){
                $info->is_verify_mobile = 1;
                $info->save();
            }
            return true;
        }
        return false;
    }

    //是否验证邮箱
    public function getIsVerifyEmail()
    {
        return $this->userInfo->is_verify_email ? true : false;
    }

    //设置认证手机号
    public  function setVerifiedMobile($mobile){
        $this->bind_mobile = $mobile;
        if($this->save()){
            return true;
        }else{
            return false;
        }
    }
    //判断手机号是否已经被他人绑定
    public  function isMobileUsed($mobile){
        $userLogin = UserLogin::findBySql('select * from user_login where username = '.$mobile.' or bind_mobile = '.$mobile)->one();
        return $userLogin ? true : false;
    }




}
