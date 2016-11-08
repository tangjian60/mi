<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/30
 * Time: 14:51
 */

namespace communal\components\resource;


use communal\components\BaseComponent;
use communal\models\df_resource\City;
use communal\models\df_resource\Province;

class Area extends BaseComponent
{
    public  $cityNumberData;
    public  $cityData;
    public  $provinceData;
    public  $provinceNumberData;
    public  function getCityDataByNum($num){
        $data = $this->getCityNumberData();
        return isset($data[$num]) ? $data[$num] :[];
    }

    public function getAreaDetailByNum($num, $isAr = FALSE, $jointer = ' ', $useEnglish = FALSE){
        if(empty($num)){
            return '';
        }
        $city = $this->getCityDataByNum($num);
        $key = $useEnglish ? 'english_name' : ($isAr ? 'abbreviation' :'name');

        if(! empty($city)){
            if($city['parent_city_id'] > 0){
                $parentCityData = $this->getCityDataById($city['parent_city_id']);
                $area = $parentCityData[$key];
            }else{
                $provinceId = $city['parent_id'];
                $province = $this->getProvinceDataById($provinceId);
                $area = isset($province[$key])? $province[$key] :'';
            }
            $area = $area ? ($area . $jointer . $city[$key]) : $city[$key];
        }else{
            $province = $this->getProvinceDataByNum($num);
            $area = isset($province[$key]) ? $province[$key] : '';
        }
        return $area;
    }

    public function getProvinceDataByNum($num){
        $data = $this->getProvinceNumberData();
        return isset($data[$num]) ? $data[$num] : false;
    }

    /**
     * 获取城市数据 数据格式为 array( cityNumber => data )
     *
     * @return array
     */
    public  function getProvinceNumberData()
    {
        if($this->provinceNumberData === NULL) {
            $this->initProviceData();
        }

        return $this->provinceNumberData;
    }

    public  function getProvinceDataById($id){
        $data = $this->getProvinceData();
        return isset($data[$id]) ? $data[$id] :false;
    }

    public  function getProvinceData(){
        if($this->provinceData ===null){
            $this->initProvinceData();
        }
        return $this->provinceData;
    }

    public  function initProvinceData(){
        $provinceData = $this->getCache('resource_area_province_data');
        $provinceNumberData = $this->getCache('resource_area_province_number_data');
        if(!$provinceData || !$provinceNumberData){
            $data = Province::find()->select([
                'id',
                'name',
                'english_name',
                'province_number',
                'abbreviation'
            ])->asArray()->all();
            $provinceData = array();
            $provinceNumberData = array();

            foreach ($data as $key => $value) {

                $provinceData[$value['id']]          = $value;
                $provinceNumberData[$value['province_number']] = $value;

                unset($provinceData[$value['id']]['id']);
                unset($provinceNumberData[$value['province_number']]['province_number']);

            }

            $this->setCache('resource_area_province_data', $provinceData);
            $this->setCache('resource_area_province_number_data', $provinceNumberData);
        }

        $this->provinceData = $provinceData;
        $this->provinceNumberData = $provinceNumberData;
    }

    public function getCityDataById($id){
        $data = $this->getCityData();
        return isset($data[$id])? $data[$id] : false;
    }

    public function getCityData(){
        if($this->cityData === null){
          $this->initCityData();
        }
        return $this->cityData;
    }

    public  function getCityNumberData(){
        if($this->cityNumberData === null){
            $this->initCityData();
        }
        return $this->cityNumberData;
    }

    public  function initCityData(){
        $cityData = $this->getCache('resource_area_city_data');
        $cityNumberData = $this->getCache('resource_area_city_number_data');
        if($cityData === false || $cityNumberData === false){
           $data = City::find()->select(['id', 'name', 'english_name', 'parent_id', 'parent_city_id', 'city_number', 'abbreviation'])
               ->where(['isValid' => 1])
               ->asArray()
               ->all();

            $cityData = [];
            $cityNumberData = [];
            foreach($data as $key=>$value){
                $cityData[$value['id']] = $value;
                $cityNumberData[$value['city_number']] = $value;
                unset($cityData[$value['id']]['id']);
                unset($cityNumberData[$value['city_number']]['city_number']);
            }

            $this->setCache('resource_area_city_data', $cityData);
            $this->setCache('resource_area_city_number_data', $cityNumberData);
        }
        $this->cityData = $cityData;
        $this->cityNumberData = $cityNumberData;
    }

}