<?php

namespace mi\modules\credit\models;

use Yii;

/**
 * This is the model class for table "mi_advertising".
 *
 * @property string $id
 * @property string $title
 * @property string $content
 * @property string $description
 * @property integer $status
 * @property integer $type
 * @property integer $align
 * @property string $addtime
 */
class MiAdvertising extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mi_advertising';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'description'], 'string'],
            [['status', 'type', 'align','addtime', 'updatetime'], 'integer'],
            [['title'], 'string', 'max' => 80],
        	//[['picture'], 'file'],
        	[['url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'content' => '内容',
            'description' => '描述',
            'status' => '是否显示',
            'type' => '类型',
            'align' => '投放位置',
        	'url' => '地址',
            'addtime' => '添加时间',
        	'updatetime' => '更新时间'
        ];
    }
    public function beforeSave($insert){
    	if (parent::beforeSave($insert)) {
    		if(isset($_REQUEST['id'])){
    			$this->updatetime = time();
    		}else{
    			$this->addtime = time();
    		}
    		return true;
    	} else {
    		return false;
    	}
    }
}
