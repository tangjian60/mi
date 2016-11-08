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
use communal\models\ve_company\Job;

class FilterJobBehavior extends AdBehavior
{
    public $eventPosition = [ Single::EVENT_FILTER, Single::EVENT_AFTER_FILTER_COMPANY, Single::EVENT_END ];

    public function handle($event)
    {
        $isSystem = $event == Single::EVENT_FILTER ? true : false;
        switch( $event->name ){
            case Single::EVENT_FILTER :
            case Single::EVENT_AFTER_FILTER_COMPANY :
                $this->filterByJob();
                break;
            case Single::EVENT_END :
                if( $this->value['number'] ){
                    $this->getJob( $isSystem );
                }
                break;
        }
    }

    public function filterByJob()
    {
        $data = $this->data;
        $cUids = ArrayHelper::getColumn($data, 'c_userid');
        $value = $this->value;

        $query = Job::find()
            ->select('t.c_userid, count(t.c_userid) as num')
            ->from('job as t')
            ->leftJoin('job_detail as d', 't.job_id = d.job_id')
            ->where(['t.c_userid' => $cUids, 't.is_recycler' => 0, 't.is_deleted' => 0, 'status' => 1])
            ->andWhere('DATE_ADD(t.modify_time, INTERVAL t.job_indate DAY) > NOW()')
            ->groupBy('t.c_userid');

        if( isset($value['salary']) )
            $query->andWhere(['>=', 'd.salary', $value['salary']]);

        if( isset($value['number']) )
            $query->having('num >=' . $value['number']);

        $list = $query->asArray()->all();
        $list = ArrayHelper::map($list, 'c_userid', 'c_userid');

        $data = array_filter($data, function( $row ) use($list){
            return isset( $list[$row['c_userid']] );
        });

        $this->setData($data);
    }

    public function getJob( $isSystem )
    {
        $data = $this->data;
        $uids = ArrayHelper::getColumn($data, 'c_userid');
        $jobData =  AdHelper::getCompanyJob( $uids, $this->value, $isSystem);
        $data = ArrayHelper::index($data, 'c_userid');

        foreach($data as $c_userid=>&$v){
            $v['jobs'] = isset($jobData[$c_userid]) ? $jobData[$c_userid] : [];
        }
        $this->setData( $data );
    }
}
