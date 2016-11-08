<?php
namespace common\extensions\utils;
/**
 * @desc	google 地图api
 * @author	lijianwei	2013-3-20
 */
class Gmap_Api{
	private $_geocoding_api_url = 'http://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=true&language=zh-CN';
	//根据地址获取经纬度   精度不是很高
	//return array(经度,纬度)  or false
	public function getLatLongByAddress($address = ''){
		$geocoding_api_url = sprintf($this->_geocoding_api_url, urlencode($address));
		
		Curl::setTimeOut(60);
		
		$json_str = Curl::curl_get_content($geocoding_api_url);
		
		if(!empty($json_str)){
			$json_arr = json_decode($json_str, TRUE);	
			if(!strncasecmp($json_arr['status'], 'ok', 2)){
				return array_values($json_arr['results'][0]['geometry']['location']);
			}else{
				return FALSE;
			}
		}
	}
}