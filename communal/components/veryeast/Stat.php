<?php

namespace communal\components\veryeast;


use communal\models\ve_core\CoreJobSearch;
use communal\models\ve_stat\StatCompanyLog;
use communal\models\ve_stat\StatEmployerIndexVote;
use yii\base\Exception;

class Stat
{

    /**
     * 投递工作
     * @param $pUserId
     * @param $cUserId
     * @param $jobId
     * @throws Exception
     */
    public static function applyJob($pUserId, $cUserId, $jobId)
    {
        $pUserId = intval($pUserId);
        $cUserId = intval($cUserId);
        $jobId = intval($jobId);

        if (!$pUserId || !$cUserId || !$jobId) {
            throw new Exception('参数错误', 0);
        }

        //core_job_search 职位申请数+1
        CoreJobSearch::updateAllCounters(['job_apply_num' => 1],[
            'c_userid' => $cUserId,
            'job_id' => $jobId
        ]);

        return StatCompanyLog::applyJob($pUserId, $cUserId, $jobId);
    }

    /**
     * 添加雇主点评
     *
     * @param integer     $cUserId     企业id
     * @param string      $companyName 企业名
     * @param integer     $pUserId     个人id
     * @param string      $personName  个人姓名
     * @param integer     $jobId       职位id
     * @param string      $jobName     职位名
     *
     * @throws \common\apis\Exception
     * @return bool
     */
    public static function addEmployerVote($cUserId, $companyName, $pUserId, $personName, $jobId, $jobName)
    {
        $cUserId = intval($cUserId);
        $companyName = trim($companyName);
        $pUserId = intval($pUserId);
        $personName = trim($personName);
        $jobId = intval($jobId);
        $jobName = trim($jobName);

        if (!$cUserId || !$companyName || !$pUserId || !$jobId || !$jobName) {
            throw new Exception('参数错误', 0);
        }
        if(!Contract::checkIsVip($cUserId)){
            return;
        }

        return StatEmployerIndexVote::addVote($cUserId, $companyName, $pUserId, $personName, $jobId, $jobName);
    }

    /**
     * 职位收藏
     * @param $pUserId
     * @param $cUserId
     * @param $jobId
     * @return mixed
     * @throws ApiException
     */
    public static function favoriteJob($pUserId, $cUserId, $jobId)
    {
        $pUserId = intval($pUserId);
        $cUserId = intval($cUserId);
        $jobId = intval($jobId);

        if (!$pUserId || !$cUserId || !$jobId) {
            throw new Exception('参数错误', 0);
        }

        return StatCompanyLog::favoriteJob($pUserId, $cUserId, $jobId);
    }

}