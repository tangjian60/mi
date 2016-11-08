<?php

namespace mi\modules\credit\models;

use Yii;

/**
 * This is the model class for table "mi_config".
 *
 * @property integer $id
 * @property string $sitetitle
 * @property string $sitetitle2
 * @property string $sitedescription
 * @property string $siteurl
 * @property string $sitekeywords
 * @property string $sitetcp
 * @property string $sitelx
 * @property string $sitelogo
 * @property integer $pingoff
 * @property integer $postovertime
 * @property integer $bookoff
 * @property integer $mood
 * @property integer $ishits
 * @property integer $iscopyfrom
 * @property integer $isauthor
 * @property string $artlistnum
 */
class MiConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mi_config';
    }
    public static function findCon()
    {
    	
    	return static::findOne();
    } 
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sitetitle', 'sitetitle2', 'sitedescription', 'siteurl', 'sitekeywords', 'sitetcp', 'sitelx', 'sitelogo'], 'string'],
            [['sitekeywords', 'sitetcp', 'sitelx', 'sitelogo'], 'required'],
            [['pingoff', 'postovertime', 'bookoff', 'mood', 'ishits', 'iscopyfrom', 'isauthor', 'artlistnum'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sitetitle' => 'Sitetitle',
            'sitetitle2' => 'Sitetitle2',
            'sitedescription' => 'Sitedescription',
            'siteurl' => 'Siteurl',
            'sitekeywords' => 'Sitekeywords',
            'sitetcp' => 'Sitetcp',
            'sitelx' => 'Sitelx',
            'sitelogo' => 'Sitelogo',
            'pingoff' => 'Pingoff',
            'postovertime' => 'Postovertime',
            'bookoff' => 'Bookoff',
            'mood' => 'Mood',
            'ishits' => 'Ishits',
            'iscopyfrom' => 'Iscopyfrom',
            'isauthor' => 'Isauthor',
            'artlistnum' => 'Artlistnum',
        ];
    }
}
