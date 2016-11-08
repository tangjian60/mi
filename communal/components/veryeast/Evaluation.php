<?php

namespace communal\components\veryeast;


use communal\components\BaseComponent;
use communal\models\df_resource\Post as PostModel;
use communal\models\ve_company\Job as JobModel;
use Yii;

class Evaluation extends BaseComponent
{
    //投递岗位是否是管理者岗位
    function isManager($jobId=0)
    {
        $jobPostNumber = $this->getPostNumber($jobId);
        $res = PostModel::find()->where([
            'post_number' => $jobPostNumber
        ])->asArray()->one();
        return intval($res['class_id']) == 4 ? false : true;
    }

    //投递岗位跟意向职位是否匹配
    function isMatch($userid=0,$jobid=0)
    {
        $person = Yii::getCommunalComponent('person',['p_userid' => $userid]);
        $desiredPosition = $person->getResumeFields(['desiredPosition']);
        $desiredPosition = isset($desiredPosition['desiredPosition'])?$desiredPosition['desiredPosition']:null;
        $job_post_number = $this->getPostNumber($jobid);//echo $job_post_number;exit;//2601

        if($desiredPosition)
        {
            foreach($desiredPosition as $v)
            {
                if(intval($job_post_number) == intval($v['position'])) return true;
            }
        }
        return false;
    }

    /**
     * 获取职位类别id
     * @param int $jobid
     * @return string
     */
    protected function getPostNumber($jobId=0)
    {
        $res = JobModel::find()->select('job_post_number')->where([
            'job_id' => $jobId
        ])->asArray()->one();
        $job_post_number = isset($res['job_post_number']) ? $res['job_post_number'] : '';

        return $job_post_number;
    }

}