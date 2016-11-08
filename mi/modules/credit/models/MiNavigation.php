<?php

namespace mi\modules\credit\models;

use Yii;

/**
 * This is the model class for table "mi_navigation".
 *
 * @property integer $id
 * @property string $title
 * @property string $picture
 * @property integer $status
 */
class MiNavigation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mi_navigation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status','addtime', 'updatetime'], 'integer'],
            [['title', 'picture'], 'string', 'max' => 255],
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
            'picture' => 'logot图片',
            'status' => '热卖显示',
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
