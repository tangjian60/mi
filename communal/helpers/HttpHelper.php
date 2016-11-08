<?php
namespace communal\helpers;
class HttpHelper
{
	/**
	 * 发送post请求
	 *
	 * @param	string $url 请求地址
	 * @param	array $data post数据
	 * @param	array $cookie 发送的cookie
	 * @param	string $timeout 超时时间
	 * @return	string 请求页的输出数据
	 */
	public static function post($url, $data = array(), $cookie = array(), $timeout = 15)
	{
		$content = "";
		if ( ! empty($data))
		{
			$content .= http_build_query($data);
		}
		$header = array('Content-Type: application/x-www-form-urlencoded', 'Accept-Language: zh-cn');
		if ( ! empty($cookie))
		{
			$header[] = 'Cookie:'.http_build_query($cookie, '', ';');
		}
		$params = array(
				'http' => array(
						'method'	=> 'POST',
						'header'	=> $header,
						'content'	=> $content,
						'timeout'	=> $timeout
					)
				);
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp)
		{
			return FALSE;
		}
		$response = @stream_get_contents($fp);
		return $response;
	}
	
	/**
	 * 发送get请求
	 *
	 * @param	string $url 请求地址
	 * @param	array $data get数据
	 * @param	array $cookie 发送的cookie
	 * @param	string $timeout 超时时间
	 * @return	string 请求页的输出数据
	 */
	public static function get($url, $data = array(), $cookie = array(), $timeout = 15)
	{
		if ( ! empty($data))
		{
			$url = (strpos($url, '?')) ? rtrim($url, '&').'&'.http_build_query($data) : $url.'?'.http_build_query($data);
		}
		$header = array('Accept-Language: zh-cn');
		if ( ! empty($cookie))
		{
			$header[] = 'Cookie:'.http_build_query($cookie, '', ';');
		}
		$params = array(
				'http' => array(
						'method'	=> 'GET',
						'header'	=> $header,
						'timeout'	=> $timeout
					)
				);
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp)
		{
			return FALSE;
		}
		$response = @stream_get_contents($fp);
		return $response;
	}
}