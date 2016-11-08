<?php
namespace common\extensions\utils;
/**
 * @desc	过滤数据
 * @author	lijianwei	2013-2-28
 */
class SafeData{
	//安全过滤  post get 数据
	public  static function safe($data, $mode = "111"){
		self::trim_deep($data);
		if(empty($data)) return $data;

		$mode_arr = preg_split("//", $mode, -1, PREG_SPLIT_NO_EMPTY);
		if(0 == $mode_arr[0]){//取消转义
			$data = self::strips_deep($data);
		}elseif(1 == $mode_arr[0]){//转义
			$data = self::addslash_deep($data);
		}

		if($mode_arr[1]) self::js_deep($data);
		if($mode_arr[2]) self::html_deep($data);
		return $data;
	}	
	//数组trim
	public static function trim_deep(&$data){
		if(is_string($data)){
			$data = trim($data);
		}elseif(is_array($data)){
			foreach($data as $k => $v){
				self::trim_deep($v);
				$data[$k] = $v;
			}
		}
		return $data;
	}
	
	//数组过滤js iframe  frameset 
	public static function js_deep(&$data){
		if(is_string($data)){
			$data = preg_replace(array("|<script[^>]*?>.*?</script>|is", "|<iframe[^>]*/?>(.*?<\s*?/iframe>)?|is", "|<frameset[^>]+>.+<\s*?/frameset>|is"),array("", "", ""), $data);
		}elseif(is_array($data)){
			foreach($data as $k => $v){
				self::js_deep($v);
				$data[$k] = $v;
			}
		}
		return $data;
	}
	
	//数组过滤html标签
	public static function html_deep(&$data){
		if (is_array($data)){
			foreach($data as $k => $v){
				$data[$k] = self::html_deep($v);
			}
		}elseif(is_string($data)) {
			$data = htmlspecialchars($data, ENT_QUOTES);
		}
		return $data;
	}

	public static function strips_deep(&$data){
		if(is_string($data)){
			if(get_magic_quotes_gpc())$data = stripslashes($data);
		}elseif(is_array($data)){
			foreach($data as $k => $v){
				self::strips_deep($v);
				$data[$k] = $v;
			}
		}
		return $data;
	}
	
	public static function addslash_deep(&$data){
		if(is_string($data)){
			if(!get_magic_quotes_gpc())$data = addslashes($data);
		}elseif(is_array($data)){
			foreach($data as $k => $v){
				self::addslash_deep($v);
				$data[$k] = $v;
			}
		}
		return $data;
	}
}