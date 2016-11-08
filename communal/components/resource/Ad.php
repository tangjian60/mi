<?php

namespace communal\components\resource;

use Yii;
use communal\components\BaseComponent;
use \communal\models\mssql_jdrc\Ad as AdModel;
use \communal\models\mssql_jdrc\SystemAds as SystemAdsModel;

/**
 * @desc 广告
 */
class Ad extends BaseComponent
{
    private $cacheKeyPrefix = __NAMESPACE__;
    
    /**
     * 通过广告placeId获取位置下的所有正在投放的广告列表
     * planStatus0等通知广告 1安排的广告 2确定时间广告 3确定位置广告 4确定时间位置 5补空广告
     * @param array/int $placeId 广告位置Id @李洋
     * @param boolean $useCache 是否使用缓存
     * @return array()
     */
    public function getAdsList($placeId, $useCache = TRUE)
    {
        if(is_array($placeId)){
            $placeId = array_map('intval', $placeId);
            $placeId = "'" . implode("','", $placeId) . "'";
        }


        $cacheKey = $this->cacheKeyPrefix . __FUNCTION__ . $placeId;
        $data = $useCache ? parent::getCache($cacheKey) : FALSE;
        if ($data === FALSE) {
            $data = AdModel::find()
                ->select('ADID, ADType, ADSrc, ADWidth, ADHeight, ADAlt, userId, placeId, position, ADSubject, planStatus')
                ->where('status = 1 AND placeId IN(' . $placeId . ')')
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
    public function getSystemAdsList($siteId, $useCache = TRUE)
    {
        if(is_array($siteId)){
            $siteId = array_map('intval', $siteId);
            $siteId = "'" . implode("','", $siteId) . "'";
        }


        $cacheKey = $this->cacheKeyPrefix . __FUNCTION__ . $siteId;
        $data = $useCache ? parent::getCache($cacheKey) : FALSE;

        if ($data === FALSE) {
            $data = SystemAdsModel::find()
                ->select('user_id AS userId, type, name, link, height, width, src, site_id')
                ->where('status = 0 AND site_id IN(' . $siteId . ')')
                ->orderBy('site_id ASC, sort ASC')
                ->asArray()
                ->all();

            if ($useCache) {
                parent::setCache($cacheKey, $data, 3600);
            }
        }

        return $data;
    }
    
}