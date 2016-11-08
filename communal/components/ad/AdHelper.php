<?php

namespace communal\components\ad;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use communal\components\BaseComponent;
use communal\models\MssqlJdrc;
use communal\models\ve_company\Job;
use communal\models\mssql_jdrc\Ad as AdModel;
use communal\models\mssql_jdrc\SystemAds as SystemAdsModel;

/**
 * @desc 广告
 */
class AdHelper extends BaseComponent
{
    private static $cacheKeyPrefix = '_ad_';

    /**
     * generate a common memcache key
     * @param $placeId
     * @param $adsType
     * @return string
     */
    public static function getAdCacheKey( $placeId, $adsType = 'recommend', $clearCache = false )
    {
        if( is_array($placeId) ){
            $placeId = array_map('intval', $placeId);
            $placeId = implode("_", $placeId);
        }
        $cacheKey = self::$cacheKeyPrefix . $adsType . $placeId;

        if( $clearCache ) parent::deleteCache($cacheKey);
        return  $cacheKey;
    }
    /**
     * 通过广告placeId获取位置下的所有正在投放的广告列表
     * planStatus0等通知广告 1安排的广告 2确定时间广告 3确定位置广告 4确定时间位置 5补空广告
     * @param array/int $placeId 广告位置Id @李洋
     * @param boolean $useCache 是否使用缓存
     * @return array()
     */
    public static function getAdsList($placeId, $useCache = TRUE)
    {

        $cacheKey = self::getAdCacheKey( $placeId, 0 );
        $data = $useCache ? parent::getCache($cacheKey) : FALSE;
        if ($data === FALSE) {
            $data = AdModel::find()
                ->select('ADID, ADType, ADSrc, ADWidth, ADHeight, ADAlt, userId, placeId, position, ADSubject, planStatus')
                ->where(['status' => 1, 'placeId' => $placeId])
                ->orderBy('placeId ASC, dOrder ASC')
                ->asArray()
                ->all();

            if ($useCache) {
                parent::setCache($cacheKey, $data, 3600);
            }
        }

        return $data;
    }
    
     /**
     * 通过系统推荐siteId获取位置下的所有有效信息
     * @param array/int $siteId 系统推荐位置Id @李洋
     * @param boolean $useCache 是否使用缓存
     * @return array()
     */
    public static function getSystemAdsList($siteId, $useCache = TRUE)
    {
        $cacheKey = self::getAdCacheKey( $siteId, 1 );
        $data = $useCache ? parent::getCache($cacheKey) : FALSE;

        if ($data === FALSE) {
            $data = SystemAdsModel::find()
                ->select('user_id AS userId, type, name, link, height, width, src, site_id')
                ->where(['status' => 0, 'site_id' => $siteId])
                ->orderBy('site_id ASC, sort ASC')
                ->asArray()
                ->all();

            if ($useCache) {
                parent::setCache($cacheKey, $data, 3600);
            }
        }

        return $data;
    }

    /**
     * get company information by given userids
     * @param array $uids
     * @return array
     */
    public static function getCompanyInfo( array $uids )
    {
        $return = [];
        if ( count($uids) ) {
            $return = MssqlJdrc::getDb()->createCommand()
                ->select('userId AS c_userid, cname AS company_name, cypic AS company_logo')
                ->from('jdrcadmin.company')
                ->where(['userId' => $uids])
                ->andWhere(['<>', 'cypic', ''])
                ->andWhere(['<>', 'cypic', 'uppic/nocypic.gif'])
                ->queryAll();
        }

        return $return;
    }

    /**
     * get company information by given condition
     * @param array $excludeUid
     * @param array $extraCondition parameter's key must in [Industry、cType、areaId], like ['Industry' => [2.3]]
     * @return array
     */
    public static function getCompanyList(array $excludeUid, array $extraCondition = [])
    {
        $contractIdQuery = (new Query)->select('ContractId')
            ->from('dbo.ContractDetail')
            ->where(['product' => 1]);

        $userIdQuery = (new Query)->select('userid')
            ->from('jdrcadmin.contract')
            ->where(['status' => 1])
            ->andWhere(['in', 'id', $contractIdQuery]);

        $subAccountUserIdQuery = (new Query)->select('subUserId as userid')
            ->from('jdrcadmin.UserSubAccount')
            ->where(['product' => 1])
            ->andWhere(['in', 'UserId', $userIdQuery]);

        $query = (new Query)->select('userId as c_userid, cname as company_name, cypic as company_log')
            ->from('jdrcadmin.company')
            ->where(['in', 'userId', $userIdQuery])
            ->OrWhere(['in', 'userId', $subAccountUserIdQuery])
            ->andWhere(['not in','userId', $excludeUid])
            ->andWhere(['<>', 'cypic', ''])
            ->andWhere(['<>', 'cypic', 'uppic/nocypic.gif']);

        foreach($extraCondition as $field=>$value)
            $query->andWhere(['in', $field, $value]);

        $result = $query->all( MssqlJdrc::getDb() );

        return $result;
    }

    /**
     * get company job by given company ids
     * @param array $uids
     * @param array $extraCondition
     * @param bool|true $isSystem
     * @return array
     */
    public static function getCompanyJob( array $uids, array $extraCondition = [], $isSystem = true )
    {
        if( empty($uids) ) return [];
        $query = Job::find()
            ->select('job.c_userid, job.job_id, job.job_name, job_detail.salary')
            ->leftJoin('job_detail', 'job.job_id=job_detail.job_id')
            ->where(['job.c_userid' => $uids, 'job.is_recycler' => 0, 'job.is_deleted' => 0, 'job.status' => 1])
            ->andWhere('DATE_ADD(job.modify_time, INTERVAL job.job_indate DAY) > NOW()');

        $isSort = false;
        if( isset($extraCondition['sort']) ){
            $isSort = true;
            if( is_array($extraCondition['sort']) ){
                $sort =  key( $extraCondition['sort'] ). " " .current( $extraCondition['sort'] );
            }else{
                $sort = $extraCondition['sort'];
            }
            $query->orderBy($sort);
        }

        if(! $isSort){
            if($isSystem)//system recommend
                $query->orderBy('job.sort ASC');
            else//random recommend
                $query->orderBy('job.modify_time DESC');
        }

        $list = $query->asArray()->all();
        $salaryThousand = Yii::getConfig('salary_thousand', '@communal/config/data/company.php');

        $result = [];
        foreach($list as $row){
            $row['salaryThousand'] = $salaryThousand[$row['salary']];
            $key = $row['c_userid'];
            if(isset($result[$key]) && count($result[$key]) > $extraCondition['number']) continue;
            unset($row['c_userid']);
            $result[$key][] = $row;
        }

        return $result;
    }


}