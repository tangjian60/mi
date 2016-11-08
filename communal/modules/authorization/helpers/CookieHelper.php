<?php 

namespace communal\modules\authorization\helpers;

use Yii;

class CookieHelper{

	const DOMAIN = 'veryeast.cn';

	public static function set_self_cookie( $cookies = array(), $expire = 0 )
	{
		$expire == 0 ? '' : $expire = $expire -time();
		foreach ($cookies as $key => $val)
		{
			$cookie_data = array(
				'name'   => $key,
				'value'  => $val,
				'expire' => $expire,
				'domain' => '.' . self::DOMAIN,
				'path'   => '/',
			);
			header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
			set_cookie( $cookie_data );
		}
	}

	public static function delete_self_cookie( $names = array( 'ticket' ) )
	{
		$domain = '.' . self::DOMAIN,
		foreach ( $names as $value )
		{
			delete_cookie( $value, $domain, '/' );
		}
	}

}
