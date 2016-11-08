<?php

namespace mi\modules\credit\models;

use Yii;

/**
 * This is the model class for table "mi_search".
 *
 * @property integer $id
 * @property string $title
 * @property integer $count
 * @property integer $addtime
 */
class MiSearch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mi_search';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'count', 'addtime'], 'integer'],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'count' => 'Count',
            'addtime' => 'Addtime',
        ];
    }
}
