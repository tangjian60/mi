<?php

namespace mi\modules\credit\models;

use Yii;

/**
 * This is the model class for table "mi_category".
 *
 * @property integer $id
 * @property string $title
 * @property string $url
 * @property integer $level
 * @property integer $category_id
 */
class MiCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mi_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'level', 'category_id'], 'integer'],
            [['title', 'url'], 'string', 'max' => 255]
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
            'url' => '地址',
            'level' => '级别',
            'category_id' => '父类',
        ];
    }
}
