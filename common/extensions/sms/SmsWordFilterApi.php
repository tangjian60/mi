<?php
/**
 * @desc    sms 敏感词    依赖apc
 * @author	lijianwei	2013-8-2
 * @deprecated  请使用common\apis\resource\SmsWordFilterApi
 */
namespace common\extensions\sms;
use common\models\DFResource;

class SmsWordFilterApi 
{
	//apc key
	private static $apc_key = 'common.extensions.sms.SmsWordFilterApi.apckey';
	//找到的敏感词
	public static $occurWords = array();
	/**
	 * 
	 * 敏感词
	 * @param string $sms_content 短信内容
	 * @param int $filter_callback  处理方法   1 字符中间加空格  2 过滤掉
	 * @return string
	 */
	public static function filter($sms_content = '', $filter_callback = 1)
	{
		
		$sms_content = trim($sms_content);
		$callback = self::callBackMap($filter_callback);
		if(!is_callable($callback)) {
			$callback = self::callBackMap(1);
		}
		
		$word_data = array();
		self::getWordData($word_data);
		foreach($word_data as $word) {
			if(false !== strpos($sms_content, "$word")) {
				self::$occurWords[] = $word;
				$sms_content = str_replace($word, call_user_func_array($callback, array($word, $sms_content)), $sms_content);
			}	
		}
		return $sms_content;
	}
	
	//获取敏感词
	protected static function getWordData(array &$word_data = array())
	{
		if(!$word_data = apc_fetch(self::$apc_key)) {
			$data = DFResource::getDb()->createCommand()
				->select('word')
				->from('{{sms_sensitive_word}}')
				->where('enabled = 1')
				->queryAll();
			$word_data = array();
			foreach($data as $k => $v) {
				$word_data[] = $v['word'];
			}
	
			apc_store(self::$apc_key, $word_data);
		}
	}
	
	protected static function callBackMap($type = 1)
	{
		$map = array(
			1 => __CLASS__. '::addSpace',
			2 => __CLASS__. '::strip',
		);
		return isset($map[$type]) ? $map[$type] : NULL;
	}
	//callback 1
	protected static function addSpace($word, $sms_content)
	{
		return implode(' ', preg_split('/(?<!^)(?!$)/u', $word));
	}
	//callback  2
	protected static function strip($word, $sms_content)
	{
		return '';
	}
}