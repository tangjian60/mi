<?php
namespace communal\helpers;

class StringHelper{
	
	/**
	 * iconv转换编码 
	 *
	 * @param	string $inCharset 原编码
	 * @param	string $outCharset 目标编码
	 * @param	mixed string or array $data 要转换编码的字符串或者数组
	 * @param	bool $ignoreError 是否忽略错误
	 * @return	mixed string or array
	 */
	public static function iconv($inCharset, $outCharset, $data, $ignoreError = TRUE)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				$data[$key] = self::iconv($inCharset, $outCharset, $val, $ignoreError);
			}
		}
		else
		{
			if (function_exists('iconv'))
			{
				$data = $ignoreError ? @iconv($inCharset, $outCharset, $data) : iconv($inCharset, $outCharset, $data);
			}
			else if (function_exists('mb_convert_encoding'))
			{
				$data = $ignoreError ? @mb_convert_encoding($data, $outCharset, $inCharset) : mb_convert_encoding($data, $outCharset, $inCharset);
			}
		}
		return $data;
	}
	
	/**
	 * Strip Slashes
	 *
	 * 删除字符串或者数组中由addslashes() 函数添加的反斜杠
	 *
	 * @param	mixed	string or array
	 * @return	mixed	string or array
	 */
	public static function stripSlashes($str)
	{
		if (is_array($str))
		{
			foreach ($str as $key => $val)
			{
				$str[$key] = self::stripSlashes($val);
			}
		}
		else
		{
			$str = stripslashes($str);
		}
		return $str;
	}
	
	/**
	 * Quotes to Entities
	 *
	 * 将单引号或者双引号转为实体
	 *
	 * @param	string $str 要转换的字符串
	 * @return	string
	 */
	public static function quotesToEntities($str)
	{
		return str_replace(array("\'","\"","'",'"'), array("&#39;","&quot;","&#39;","&quot;"), $str);
	}
	
	/**
	 * Create a Random String
	 *
	 * 生成一个随机字符串
	 *
	 * @param	string	$type 随机字符串类型.  basic, alpha, alunum, numeric, nozero, unique, md5
	 * @param	integer	$len 随机字符长度
	 * @return	string
	 */
	public static function randomString($type = 'alnum', $len = 8)
	{
		switch($type)
		{
			case 'basic'	: 
				return mt_rand();
				break;
			case 'alnum'	:
			case 'numeric'	:
			case 'nozero'	:
			case 'alpha'	:
				switch ($type)
				{
					case 'alpha'	:	
						$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'alnum'	:	
						$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'numeric'	:	
						$pool = '0123456789';
						break;
					case 'nozero'	:	
						$pool = '123456789';
						break;
				}
				$str = '';
				for ($i=0; $i < $len; $i++)
				{
					$str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
				}
				return $str;
				break;
			case 'unique'	:
			case 'md5'		:
				return md5(uniqid(mt_rand()));
				break;
		}
	}

	/**
	 * 字符串截取，按字节截取
	 *
	 * @param string $str
	 * @param intval length
	 * @param string $flow
	 * @param string $charset
	 * 
	 * @return string
	 */
	public static function cut($str, $length, $flow = '...', $charset = 'UTF-8')
	{
		$length = intval($length);
		$end = strlen($str) > $length ? $flow : '';
		return mb_strcut($str, 0, $length, $charset) . $end;
	}
	
}