<?php

namespace mi\modules\credit\models;

use Yii;

/**
 * This is the model class for table "mi_ce".
 *
 * @property integer $id
 * @property integer $browse
 * @property integer $visitors
 * @property string $ip
 * @property integer $nvisitors
 * @property integer $visits
 * @property string $name
 * @property string $region
 * @property string $addtime
 */
class MiCe extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mi_ce';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['browse', 'visitors', 'nvisitors', 'visits'], 'integer'],
            [['ip', 'name', 'region', 'addtime'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '序号',
            'browse' => '渠道号',
            'visitors' => '基础激活',
            'ip' => '质量奖励1',
            'nvisitors' => '质量奖励2',
            'visits' => '结算激活',
            'name' => '名字',
            'region' => '地区',
            'addtime' => '日期',
        ];
    }
}
