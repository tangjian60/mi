<?php

namespace mi\modules\credit\models;

use Yii;
use yii\web\IdentityInterface;
/**
 * This is the model class for table "bao_bduser".
 *
 * @property integer $id
 * @property string $username
 * @property string $pwd
 * @property string $phone
 * @property string $remarks
 * @property integer $group_id
 * @property integer $create_time
 * @property integer $updte_time
 * @property integer $status
 */
class MiBduser extends  \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mi_bduser';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'pwd', 'remarks'], 'required'],
            [['id', 'group_id', 'create_time', 'status'], 'integer'],
            [['username', 'pwd', 'remarks'], 'string', 'max' => 50],
            [['phone'], 'string', 'max' => 11]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'pwd' => '密码',
            'phone' => '手机号',
            'remarks' => '描述',
            'group_id' => '用户组id',
            'create_time' => '创建时间',
            'updte_time' => '更新时间',
            'status' => '状态',
        ];
    }
    public static function findByUsername($username)
    {
    	return static::findOne(['username' => $username]);
    }
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
    	throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
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
    public function validateAuthKey($authKey)
    {
    	return $this->getAuthKey() === $authKey;
    }
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
    	return static::findOne(['id' => $id]);
    }
    public function beforeSave($insert){
    	if (parent::beforeSave($insert)) {
    		if(isset($_REQUEST['id'])){
    			$this->update_time = time();
    		}else{
    			$this->create_time = time();
    		}
    		return true;
    	} else {
    		return false;
    	}
    }
}
