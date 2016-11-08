<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace communal\components\ad;

use Yii;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use communal\models\ve_company\Company;
use communal\models\ve_company\CompanyDetail;

/**
 * filter company by company industry, or company type, or company area.
 * if trigger Single::EVENT_FILTER_COMPANY, then will generate rand recommend company that already filter,
 * otherwise, will filter company which given
 * @see AdHelper::getCompanyList
 */
class FilterCompanyBehavior extends AdBehavior
{
    public $eventPosition = [Single::EVENT_FILTER, Single::EVENT_FILTER_COMPANY ];

    public function handle($event)
    {
        if($event->name == Single::EVENT_FILTER_COMPANY){

            $data = AdHelper::getCompanyList( $event->excludeUids, $this->value );
            $this->setData($data);

        }else{
            foreach($this->value as $field=>$value){
                call_user_func( [$this, 'filter_' . $field] );
            }
        }
    }

    /**
     * filter by company industry
     */
    public function filter_Industry(){
        $data = $this->data;
        $cUids = ArrayHelper::getColumn($data, 'c_userid');
        $value = $this->value['Industry'];

        $list = CompanyDetail::find()
            ->select('c_userid')
            ->where(['c_userid' => $cUids, 'company_industry' => $value])
            ->asArray()
            ->all();
        $list = ArrayHelper::map($list, 'c_userid', 'c_userid');

        $data = array_filter($data, function( $row ) use($list){
            return isset( $list[$row['c_userid']] );
        });

        $this->setData($data);
    }

    /**
     * filter by company type
     */
    public function filter_cType()
    {
        $data = $this->data;
        $cUids = ArrayHelper::getColumn($data, 'c_userid');
        $value = $this->value['cType'];

        $list = CompanyDetail::find()
            ->select('c_userid')
            ->where(['c_userid' => $cUids,'company_type' => $value])
            ->asArray()
            ->all();
        $list = ArrayHelper::map($list, 'c_userid', 'c_userid');

        $data = array_filter($data, function( $row ) use($list){
            return isset( $list[$row['c_userid']] );
        });

        $this->setData($data);
    }

    /**
     * filter company by company area
     */
    public function filter_areaId()
    {
        $data = $this->data;
        $cUids = ArrayHelper::getColumn($data, 'c_userid');
        $value = $this->value['areaId'];

        $query = Company::find()
            ->select('c_userid')
            ->where(['c_userid' => $cUids]);

        foreach( (array) $value as $v){
            $query->andWhere(['like', 'current_location', sprintf('%02d', $v) . '%', false]);
        }

        $list = $query->asArray()->all();
        $list = ArrayHelper::map($list, 'c_userid', 'c_userid');

        $data = array_filter($data, function( $row ) use($list){
            return isset( $list[$row['c_userid']] );
        });

        $this->setData($data);
    }

}
