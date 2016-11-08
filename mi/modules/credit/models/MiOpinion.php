<?php

namespace mi\modules\credit\models;

use Yii;

/**
 * This is the model class for table "mi_opinion".
 *
 * @property integer $id
 * @property string $content
 * @property integer $contact
 * @property string $addtime
 */
class MiOpinion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mi_opinion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'contact'], 'integer'],
            [['content', 'addtime'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => '反馈内容',
            'contact' => '联系方式',
            'addtime' => 'Addtime',
        ];
    }
    public function beforeSave($insert){
    	if (parent::beforeSave($insert)) {
    		$this->addtime = date('Y-m-d h:i:s',time());
    		return true;
    	} else {
    		return false;
    	}
    }
}
