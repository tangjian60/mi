<?php 

namespace communal\modules\authorization\helpers;

class SsoHelper
{
	function respond_request( $type='get', $param = array(), $redirect = '', $appid = 1 )
	{
		$CI = & get_instance();
		$type = strtolower( $type );
		switch ( $type )
		{
			case 'script':
			case 'iframe':
				{	
					$param['action'] 				= $type . '_callback';
					isset( $param['data'] ) 		&& $param['data'] = serialize( $param['data'] );
					isset( $param['scripts'] )		&& $param['scripts'] = serialize( array_keys( $param['scripts'] ) );
					isset( $_REQUEST['callback'] )	&& $param['callback'] = $_REQUEST['callback'];
					isset( $param['user_data'][2] ) && $param['user_data'][2] = urlencode($param['user_data'][2]);
					isset( $param['user_data'] )	&& $param['user_data'] = serialize($param['user_data']);	//set_old_cookie need
					redirect( authcode_url( $appid, $param ) );
				}
				break;
			case 'get':
				{
					$redirect = sso_build_url($redirect, $param);
					redirect( $redirect );
				}
				break;
			case 'callback_json':
				{
					$callback_name	= $CI->input->get_post('callback_name') ? $CI->input->get_post('callback_name') : 'callback';
					$callback		= $CI->input->get_post( $callback_name );
					echo $callback."(".json_encode($param).")";
					exit;
				}
				break;
			case 'json':
				{
					header("Content-type: application/json");
					echo json_encode($param);
					exit;
				}
				break;
			case 'xjson':
				{
					header("x-json:". json_encode($param));
					exit;
				}
				break;
			case 'xml':
				{
					header("Content-type: text/xml");
					echo '<?xml version="1.0" encoding="UTF-8"?>';
					echo xml_encode($param);
					exit;
				}
				break;
			case 'serialize':
				{
					echo serialize($param);
					exit;
				}
				break;
			case 'link':
			default:
				{
					respond_load_view( $param , $redirect , $appid );
				}
				break;
		}
	}

	function respond_load_view( $param = array(), $redirect = '', $appid = 1 )
	{
		$CI = & get_instance();
		$CI->load->vars( sso_source_path( array('path_js','path_css','path_image') ) );
		$app_webs = $CI->config->item( 'sso_web_site' );
		$action = isset( $param['action'] ) ? $param['action'] : '';
		$data = $param;
		$data['redirect'] = ($redirect ? $redirect : $app_webs[$appid]['home']);
		switch ( $action )
		{
			case 'login':
				{
					if( $data['data']['flag'] == 0 )
					{
						$scrpit_param = array(
								'action'	=> 'setcookie',
								'cookies'	=> $param['cookies'],
								'expire'	=> $param['expire']
								);
						$data['scripts'][] = authcode_url( $appid, $scrpit_param );
					}
					else
					{
						isset($_SERVER['HTTP_REFERER']) && $data['redirect'] = $_SERVER['HTTP_REFERER'];
					}
					$CI->load->view( 'user/login', $data );
				}
				break;
			case 'register':
				{
					
				}
				break;
			case 'email_verify':
				{
					$data['redirect'] = $redirect ? $redirect : (isset($app_webs[$appid]['active_redirect_url']) ? $app_webs[$appid]['active_redirect_url'] : $app_webs[$appid]['home']);
					$CI->load->view( 'user/'.$action, $data );
				}
				break;
			case 'forget_password':
			case 'reset_password':
			case 'logout':
				{
					$CI->load->view( 'user/'.$action, $data );
				}
				break;
			default:
				{
					show_404();
				}
				break;
		}
	}

	function custom_register_user( $userid, $user_type, $appid = 1 )
	{
		$CI =& get_instance();
		switch( $user_type )
		{
			case 1:
				{
					call_mysql_model_func('company_register', array($userid));
					call_mssql_func( 'user_register', array($userid, 'company', $appid) );
					
					call_mssql_func('company_register', array($userid));
				}
				break;
			case 2:
				{
					call_mssql_func( 'user_register', array($userid, 'person', $appid) );
					call_mssql_func( 'person_register', array($userid) );
					call_mysql_model_func('resume_register', array($userid));
					/* switch( $CI->input->post('person_type') )
					{
						case 'xzhome':
							{
								call_mssql_func( 'xzhome_register', array($userid) );
							}
							break;
						case 'uc':
							{
								call_mssql_func( 'uc_register', array($userid) );
							}
							break;
					} */
				}
				break;
			case 3:
				{
					call_mssql_func( 'user_register', array($userid, 'college', $appid) );
					call_mssql_func( 'college_register', array($userid) );
				}
				break;
			case 4:
				{
					call_mssql_func( 'user_register', array($userid, 'business', $appid) );
					call_mssql_func( 'business_register', array($userid) );
				}
				break;
		}
	}

	function mssql_login_user( $user_info, $flag, $appid = 1 )
	{
		$CI =& get_instance();
		$ip = $CI->input->get_post('ip') ? $CI->input->get_post('ip') : $CI->input->ip_address();
		if( $ip )
			$address = get_location_by_ip( $ip );
		if( $appid == 11 )
			$address = 'Android';
		if( $appid == 12 )
			$address = 'iPhone';
		$login_status = ($flag == 0 ? 1 : 0);
		call_mssql_func( 'user_write_log', array($user_info['userid'], $user_info['username'], $ip, $address, $user_info['user_type'], $login_status) );
	}

	function get_email_template( $action, $appid = 1, $user_type = 2 )
	{
		$CI = & get_instance();
		$CI->load->config('email_info');
		$email_param = $CI->config->item( 'email_param' );
		switch ($action)
		{
			case 'register':
				{
					switch ($user_type)
					{
						case 1:
							$param = $email_param['register_company'];
							break;
						case 2:
						default:
							{
								switch ($appid)
								{
									case 2:
										$param = $email_param['register_person_9first'];
										break;
									case 3:
										$param = $email_param['register_person_meadin'];
										break;
									case 1:
									default:
										$param = $email_param['register_person'];
										break;
								}
							}
							break;
					}
				}
				break;
			case 'email_verify':
				{
					$param = $email_param['email_verify'];
				}
				break;
			case 'reset_password':
				{
					switch ($appid)
					{
						case 2:
							$param = $email_param['reset_password_9first'];
							break;
						case 3:
							$param = $email_param['reset_password_meadin'];
							break;
						case 1:
						default:
							$param = $email_param['reset_password'];
							break;
					}
				}
				break;
		}
		$template_info = call_mssql_func('sendmail_get_template', array($param['template_id']) );
		$param['content'] = $template_info['contents'];
		$param['title'] = $template_info['title'];
		
		return $param;
	}

	function sso_send_mail( $template, $email = '', $data = array() )
	{
		$CI = & get_instance();
		$CI->load->library('cmail');
		$CI->cmail->sso_send_mail( $template, $email, $data );
	}

	function get_location_by_ip( $ip )
	{
		$CI = & get_instance();
		$CI->load->library('ip_location');
		$ip_location = new Ip_location();
		return $ip_location->get_location( $ip );
	}

	function authcode($string, $operation = 'DECODE', $key = SSO_TICKET_KEY, $expiry = 0) {

		$ckey_length = 5;	// 随机密钥长度 取值 0-32;
		// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
		// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
		// 当此值为 0 时，则不产生随机密钥

		$key = md5($key);
		$keya = md5(substr($key, 0, 16));
		$keyb = md5(substr($key, 16, 16));
		$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

		$cryptkey = $keya.md5($keya.$keyc);
		$key_length = strlen($cryptkey);

		$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
		$string_length = strlen($string);

		$result = '';
		$box = range(0, 255);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($cryptkey[$i % $key_length]);
		}

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		if($operation == 'DECODE') {
			if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
				return substr($result, 26);
			} else {
				return '';
			}
		} else {
			return $keyc.str_replace('=', '', base64_encode($result));
		}
	}

	function set_self_cookie( $cookies = array(), $expire = 0 )
	{
		$expire == 0 ? '' : $expire = $expire -time();
		foreach ($cookies as $key => $val)
		{
			$cookie_data = array(
					'name'   => $key,
					'value'  => $val,
					'expire' => $expire,
					'domain' => '.'.SELF_DOMAIN,
					'path'   => '/',
			);
			header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
			set_cookie( $cookie_data );
		}
	}

	function delete_self_cookie( $names = array( 'ticket' ) )
	{
		$domain = '.'.SELF_DOMAIN;
		foreach ( $names as $value )
		{
			delete_cookie( $value, $domain, '/' );
		}
	}

	function authcode_url( $appid, $param )
	{
		$CI = & get_instance();
		$app_webs = $CI->config->item( 'sso_web_site' );
		$appid = isset( $app_webs[$appid] ) ? $appid : 1;
		
		$sign = '';
		$code = '';
		foreach ( $param as $key => $val )
		{
			$code .= $sign . $key . '=' . $val;
			$sign = '&';
		}
		$domain = sso_get_domain();
		$domain = ($appid != 1) ? $app_webs[$appid]['domain'] : $domain;//$domain ? $domain : $app_webs[$appid]['domain'];
		
		$api_urls = $CI->config->item('api_url');
		$api_url = isset($api_urls[$domain]) ? $api_urls[$domain] : reset($api_urls);
		
		$authcode_url = $api_url . '?code=' . urlencode(authcode( $code, '', $app_webs[$appid]['api_key'] ));
		
		return $authcode_url;
	}

	function sso_authcode_url( $base_url, $param, $authkey = SSO_COMMON_KEY  )
	{
		$sign = '';
		$code = '';
		foreach ( $param as $key => $val )
		{
			$code .= $sign . $key . '=' . $val;
			$sign = '&';
		}
		return $base_url . ( strpos( $base_url, '?' ) ? '&' : '?' ) . 'code=' . urlencode( ( authcode( $code, '', $authkey ) ) );
	}

	function get_current_apps( $appid = '' )
	{
		$CI = & get_instance();
		$res = array();
		$current_app = $CI->input->cookie( 'current_app' );	
		$current_app = $current_app ? unserialize($current_app) : array();
		//$appid && $current_app[ $appid ] = 1;
		$appid && $current_app = array( 2=>1, 3=>1, 4=>1 );	//delete old cookie need
		if( ! empty( $current_app ) )
		{
			foreach ( $current_app as $key => $val )
			{
				if( $key <> 1 )
					$res[$key] = authcode_url( $key, array( 'action'=>'delete_cookie' ) );
			}
		}
		return $res;
	}

	function sso_get_appid()
	{
		$CI = & get_instance();
		$app_webs = $CI->config->item( 'sso_web_site' );
		$appid = 1;
		if( $domain = sso_get_domain() )
		{
			$domain_name = substr($domain, 0, strpos($domain, '.'));
			foreach ( $app_webs as $key => $val )
			{
				if( $val['domain_name'] == $domain_name )
				{
					$appid = $key;
					break;
				}
			}
		}
		return $appid;
	}

	function sso_get_domain()
	{
		if( isset( $_SERVER['HTTP_REFERER'] ) && preg_match( '/^http\:\/\/([^\/]+)\/*.*$/', $_SERVER['HTTP_REFERER'], $match ) )
		{
			return preg_replace( '/.*\.([^\.]+\.[^\.]+)$/', '$1', $match[1] );
		}
	}

	function sso_get_expire()
	{
		$CI = & get_instance();
		$is_store_login = $CI->input->get_post( 'is_store_login' );
		$store_login_time = intval($CI->input->get_post('store_login_time'));
		if($is_store_login || $store_login_time > 0)
		{
			$expire = ( $store_login_time > 0 ? ( $store_login_time*24*3600 + time() ) : ( time() + TICKET_DEFAULT_EXPIRY ) );
		}
		else
		{
			$expire = 0;
		}
		return $expire;
	}

	function sso_get_ticket( $userid, $user_type = 1, $expiry = TICKET_DEFAULT_EXPIRY, $record_login = true, $authkey = SSO_TICKET_KEY , $digital = '')
	{
		$CI = & get_instance();
		$digital = $digital ? $digital : sso_random(9);
		$issue_time = time();
		$avail_time = $issue_time + $expiry;
		$ticket = authcode( $userid.'\t'.$user_type.'\t'.$digital.'\t'.$avail_time, '', $authkey );
		if( $record_login )
		{
			$ip = $CI->input->get_post('ip') ? $CI->input->get_post('ip') : $CI->input->ip_address();
			$address = get_location_by_ip( $ip );
			if( call_mysql_func( 'f_record_login', array($userid, $ip, $address, $ticket, date('Y-m-d H:i:s', $avail_time), $digital) ) == 0 )
			{
				return $ticket;
			}
		}
		else
		{
			call_mysql_func( 'f_add_ticket', array($userid, $ticket, date('Y-m-d H:i:s', $avail_time), $digital) );
			return $ticket;
		}
	}

	function sso_random($length) 
	{
		$hash = '';
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++) 
		{
			$hash .= $chars[mt_rand(0, $max)];
		}
		return $hash;
	}

	function sso_number_random($length)
	{
		$hash = '';
		$chars = '0123456789';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++)
		{
			$hash .= $chars[mt_rand(0, $max)];
		}
		return $hash;
	}

	function sso_parse_str( $str, array &$arr = null )
	{
		$param = explode( '&', $str );
		if( !empty( $param ) )
		{
			foreach( $param as $value )
			{
				@list( $key, $val ) = explode( '=', $value );
				$arr[$key] = $val;
			}
		}
	}

	function sso_check_password( $enter_password, $password, $userid = 0, $password_type = 'default' )
	{
		switch ($password_type)
		{
			case 'md5':
				{
					$enter_password_length = strlen($enter_password);
					switch ($enter_password_length)
					{
						case 16:
							{
								(strlen($password) == 32) && $password = substr($password, 8, 16);
								if( $enter_password == $password )
								{
									return TRUE;
								}
							}
							break;
						case 32:
							{
								(strlen($password) == 16) && $enter_password = substr($enter_password, 8, 16);
								if( $enter_password == $password )
								{
									return TRUE;
								}
							}
							break;
						default:
							{
								return sso_check_password($enter_password, $password, $userid);
							}
							break;
					}
				}
				break;
			default:
				{
					$res_password = md5($enter_password);
					if( $res_password == $password )
					{
						return TRUE;
					}
					else if( strlen($password) == 16 )
					{
						if( substr($res_password, 8, 16) == $password )
						{
							$userid > 0 && (strlen($res_password) == 32) && call_mysql_func( 'f_set_field', array( $userid, 'user', 'password', $res_password ) );
							return TRUE;
						}
					}
				}
				break;
		}
	}

	function get_function_param( $class_name, $method_name = '' )
	{
		$class = new ReflectionClass( $class_name );
		$method = $class->getMethod( $method_name );
		$param = array();
		if( $method->isPublic() && ! $method->isConstructor() )
		{
			foreach ( $method->getParameters() as $parameter )
			{
				$param[] = $parameter->getName();
			}
		}
		return $param;
	}

	function get_form_data( $param, $type = 'get_post' )
	{
		$data = array();
		$type = in_array( $type, array( 'post', 'get', 'get_post' ) ) ? $type : 'get_post';
		if( ! empty( $param ) )
		{
			$CI = & get_instance();
			foreach( $param as $value )
			{
				$data[] = $CI->input->$type( $value );
			}
		}
		return $data;
	}

	function xml_encode( $data, $encode = 'UTF-8' )
	{
		if( is_object( $data ) )
		{
			$data = get_object_vars( $data );
		}
		$res = '';
		if( !empty( $data ) )
		{
			foreach( $data as $k=>$v )
			{
				is_numeric($k) && $k="item id='{$k}'";
				$res .= "<{$k}>";
				$res .= (is_array($v)||is_object($v)) ? xml_encode($v) : ((strlen($v)>0 && ! preg_match('/^[\w\+_\/]+$/', $v)) ? "<![CDATA[{$v}]]>" : $v);
				list($k ,) = explode(' ',$k);
				$res .= "</{$k}>\n";
			}
		}
		return $res;
	}

	function get_show_email( $email = '' )
	{
		$email = trim($email);
		if(preg_match('/(.*)@([^@]*)$/', $email, $match))
		{
			$res['email'] = hide_email($match[1]) . '@' . $match[2];
			$CI = & get_instance();
			$CI->load->config('email_info');
			$email_suffix_relation = $CI->config->item( 'email_suffix_relation' );
			$match[2] = strtolower( $match[2] );
			if( isset( $email_suffix_relation[$match[2]] ) )
			{
				$res['type'] = $email_suffix_relation[$match[2]];
			}
		}
		else
		{
			$res['email'] = hide_email($email);
		}
		return $res;
	}

	function hide_email( $str = '' )
	{
		$len = strlen($str);
		if( $len > 7 )
			$hide_len = 4;
		else if( $len > 4 )
			$hide_len = 3;
		else if( $len > 2 )
			$hide_len = 2;
		else if( $len == 2 )
			$hide_len = 1;
		else
			$hide_len = 0;
		return $hide_len ? preg_replace('/(.{'.$hide_len.'})$/', str_repeat('*', $hide_len), $str) : $str;
	}

	function decode_ticket( $ticket = '' )
	{
		if( $res = authcode( $ticket, 'DECODE', SSO_TICKET_KEY ) )
		{
			$ticket_info = explode('\t', $res);
			return $ticket_info;
		}
	}

	function set_old_cookie( $data )
	{
		$api_syskey = 'jdrc';
		$data['username'] = mb_convert_encoding($data['username'], 'GBK', 'UTF-8');
		$syskey = substr(md5($data['username'].$api_syskey), 8, 16);
		
		if (isset($data['password_type']) && $data['password_type'] == 'md5')
		{
			if (strlen($data['password']) == 32)
			{
				$data['password'] = substr($data['password'], 8, 16);
			}
			$url_param = '?'.'v='.time().sso_random(10).'&syskey='.$syskey.'&uid='.$data['userid'].'&username='.urlencode($data['username']).'&password='.urlencode($data['password']).'&pass=1';
		}
		else
		{
			$url_param = '?'.'v='.time().sso_random(10).'&syskey='.$syskey.'&uid='.$data['userid'].'&username='.urlencode($data['username']).'&password='.urlencode($data['password']);
		}
		
		if (isset($data['ticket']))
			$url_param .= '&ticket=' . urlencode($data['ticket']);
		
		$apps_api = array(
				'http://www.veryeast.cn/dpo.asp',
				'http://www.9first.com/dpo.asp',
				//'http://www.meadin.com/passport/dpo.asp'
		);
		foreach($apps_api as &$value)
		{
			$value .= $url_param;
		}
		return $apps_api;
	}

	function get_recommend_username( $username = '' )
	{
		$res = array();
		if( preg_match( '/^[0-9a-zA-Z\@\.\_]{3,19}$/', $username ) )
		{
			if( ! preg_match('/^[\.\@\_]+$/', $username) )
				$user[] = substr($username.$username, 0, 20);
			$user[] = substr($username.date('Y'), 0, 20);
			$user[] = substr($username.sprintf("%02.0f", rand(0,99)), 0, 20);
			$user = array_unique( $user );
			foreach ($user as $val)
			{
				if( call_mysql_func( 'f_is_exist_field', array('user', 'username', $val) ) == 1011 )
					$res[] = $val;
			}
		}
		return $res;
	}

	function get_default_redirect($appid = 1, $user_type = 2)
	{
		$CI = & get_instance();
		$app_webs 	= $CI->config->item( 'sso_web_site' );
		return isset($app_webs[$appid]["home_{$user_type}"]) ? $app_webs[$appid]["home_{$user_type}"] : $app_webs[$appid]['home'];
	}

	function sso_build_url($url, $param = array(), $is_urlencode = false)
	{
		if ( ! empty($param) && is_array($param))
		{
			$is_sign = strpos($url, '?') ? 0 : 1;
			foreach ($param as $key => $val)
			{
				if ($is_urlencode)
					$val = urlencode($val);
				
				if ($is_sign)
					$url .= '?' . $key . '=' . $val;
				else
					$url .= '&' . $key . '=' . $val;
				$is_sign = 0;
			}
		}
		return $url;
	}
}