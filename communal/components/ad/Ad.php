<?php

namespace communal\components\ad;

use Yii;
use yii\helpers\ArrayHelper;
use communal\components\BaseComponent;

/**
 * ad component
 * e.g.
 * configurate when initialize component
Yii::getCommunalComponent('ad', ['config' => [
  'new'=>[
        'placeId' => 504,
        'number' => 8,
        'filterCompany' => [
            'Industry' => [1],
            'cType' => [2,3,4,5],
            'areaId' => [9],
        ],
        'filterJob' => [
            'salary' => 5,
            'number' => 4,
            'sort' => ['job_detail.salary'=> 'desc'],
        ],
        ]]])->getRecommend(false);
 or after initialize component ;
 $ad = Yii::getCommunal('ad')->getAds('hotel.new');
 */
class Ad extends BaseComponent
{
    public $config;
    public $excludeUids;

    /**
     * get ad list through config key, @see '@communal/components/ad/config/main.php'
     * @param $index
     * @param bool|true $useCache
     * @param bool|false $clearCache
     * @return array
     */
    public function getAds( $index, $useCache = true, $clearCache = false )
    {
        $this->config = Yii::getConfig( $index, '@communal/components/ad/config/main.php');
        return $this->getRecommend( $useCache, $clearCache );
    }
    /**
     * get recommend ad list
     * @param bool|true $useCache
     * @return array
     */
    public function getRecommend( $useCache = true, $clearCache = false ){

        if( isset( $this->config['placeId'] ) ){

            $single = $this->getSingleAd( $this->config );
            return $single->getCompanyRecommend( $useCache );

        }else{

            $result = [];
            foreach($this->config as $key=>$place){

                $single = $this->getSingleAd( $place );
                $result[$key] = $single->getCompanyRecommend( $useCache );

            }

            return $result;
        }
    }

    /**
     * get single ad component
     * @param $config single component configuration
     * @return object
     */
    public function getSingleAd( $config )
    {
        if( !isset($config['class']) ) $config['class'] = Single::className();
        $single = Yii::createObject($config);

        foreach( $single as $property=>$value ){
            if($value === null) continue;

            $class = ucfirst( $property ) . 'Behavior';
            $behaviorConfig = [
                'class' => __NAMESPACE__ . '\\' . $class,
                'value' => $value,
            ];
            $single->attachBehavior($property, $behaviorConfig);
        }

        return $single;
    }

}