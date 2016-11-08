<?php

namespace communal\components\ad;

use Yii;
use yii\helpers\ArrayHelper;
use communal\components\BaseComponent;
use communal\models\MsslqJdrc;

/**
 * single ad component
 */
class Single extends BaseComponent
{
    /** @var  $placeId system ad place id */
    private $placeId;
    /** @var  $siteId system information site id */
    private $siteId;
    /**
     * ad type
     * when set placeId, then 1,
     * when set siteId ,then 0
     * @var int $adsType ,
     */
    private $adsType = 1;

    /**
     * @var $number number of ads at this place
     * @see NumberBehavior
     */
    public $number;
    /**
     * @var $benefit benefit tags of company
     * @see BenefitBehavior
     */
    public $benefit;
    /**
     * @var $filterCompany filter company by [Industry、cType、areaId],
     * @see FilterCompanyBehavior
     */
    public $filterCompany;
    /**
     * @var $filterJob filter job
     * @see FilterJobBehavior
     */
    public $filterJob;


    protected $excludeUids = [];
    private $_data;

    const EVENT_BEFORE              = 'before';
    const EVENT_FILTER_COMPANY      = 'filterCompany';
    const EVENT_FILTER              = 'filter';
    const EVENT_END                 = 'end';
    const EVENT_AFTER_FILTER_COMPANY= 'afterFilterCompany';

    public function init()
    {
        parent::init();
        if( $this->siteId )
            $this->adsType = 0;
    }

    public function setPlaceId($value)
    {
        $this->placeId = $value;
    }

    public function setSiteId($value)
    {
        $this->siteId = $value;
    }

    public function getProperty( $property )
    {
        return $this->$property;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setData($value)
    {
        $this->_data = $value;
    }

    public function getCompanyRecommend( $useCache = true, $clearCache = false )
    {
        $this->trigger( self::EVENT_BEFORE, new AdEvent());

        $cacheKey = AdHelper::getAdCacheKey( $this->placeId, $this->adsType . 'recommend', $clearCache );
        $companyList = $useCache ? parent::getCache($cacheKey) : FALSE;

        if ($companyList === FALSE) {

            $companyList = $this->adsType ? $this->getRecommendAdsList() : $this->getRecommendSystemAdsList();
            $companyList = ArrayHelper::merge($companyList['sys'], $companyList['ext']);

            $this->triggerEvent( self::EVENT_END, $companyList);
            if ($useCache) {
                parent::setCache($cacheKey, $companyList, 3600);
            }
        }

        return $companyList;
}

    public function getRecommendAdsList()
    {
        $data = $list = $fillEmpty = $recommendSys = [];

        $adsData = AdHelper::getAdsList($this->placeId, False);
        if( !empty($adsData) ) {
            foreach ($adsData as $key => $value) {
                if($value['planStatus'] == 5) {

                    $fillEmpty[] = array(
                        'c_userid'=>$value['userId'],
                        'company_name'=>$value['ADSubject'],
                        'company_logo'=>$value['ADSrc']
                    );

                } else {

                    $data[] = array(
                        'c_userid'=>$value['userId'],
                        'company_name'=>$value['ADSubject'],
                        'company_logo'=>$value['ADSrc']
                    );

                }
            }
        }

        $this->triggerEvent( self::EVENT_FILTER, $data );

        if($this->number > count($data)) {

            if( !empty($fillEmpty) ) {
                $this->triggerEvent( self::EVENT_FILTER, $fillEmpty );
                $list  = $this->slice($fillEmpty, $data);
            }

            if( $this->number > ( count($data) + count($list) )) {
                $allTypeData = [];
                $this->triggerEvent( self::EVENT_FILTER_COMPANY, $allTypeData );

                if( !empty($allTypeData) ){
                    $this->triggerEvent( self::EVENT_AFTER_FILTER_COMPANY, $allTypeData);
                    $recommendSys = $this->slice($allTypeData, $data, $list);
                }

                $list = array_merge($list, $recommendSys);
            }
        }

        return ['sys' => $data, 'ext' => $list];
    }

    public function getRecommendSystemAdsList()
    {
        $adsData = AdHelper::getSystemAdsList($this->placeId, FALSE);
        $adsUid = $list = [];
        if (!empty($adsData)) {

            $adsData = $this->slice($adsData);
            $adsUid = ArrayHelper::getColumn($adsData, 'userId');

        }

        $companyList = AdHelper::getCompanyInfo($adsUid);
        $this->trigger( self::EVENT_FILTER, $companyList);

        if ($this->number > count($companyList)) {
            $this->excludeUids = array_merge([0], $this->excludeUids, $adsUid);
            $list = [];
            $this->trigger( self::EVENT_FILTER_COMPANY, $list);

            if( !empty( $list ) ){
                $this->triggerEvent( self::EVENT_AFTER_FILTER_COMPANY , $list);
                $this->slice( $list, $companyList );
            }

        }

        return ['sys' => $companyList, 'ext' => $list];
    }

    private function slice( array $need )
    {
        shuffle($need);
        $args = func_get_args();
        array_shift($args);
        $number = array_sum(array_map(function($param){
            return count($param);
        }, $args));

        if($this->number > $number){
            $need = array_slice($need, 0, $this->number - $number);
        }

        return $need;
    }

    /**
     * trigger ad event
     * @param $name
     * @param $data
     */
    private function triggerEvent( $name, &$data )
    {
        $this->setData( $data );
        $event = new AdEvent();
        $event->excludeUids = $this->excludeUids;
        $event->processData = $data;
        parent::trigger($name, $event);
        $data = $this->getData();
    }

}