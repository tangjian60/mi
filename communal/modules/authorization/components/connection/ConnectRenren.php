<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once dirname(__FILE__).'/Connect_base.php';
class Connect_renren extends Connect_base
{
	public $refresh_token;
	
	function redirect_bind($status = '')
	{
		$param = array();
		if (preg_match('/^\d{3}0$/', $status))
		{
			$url = site_url('connect/link/'.$this->_config['cooperate']);
		}
		else
		{
			$cookie['connect_cooperate'] = $this->_config['cooperate'];
			$cookie['connect_code'] = $this->encode($this->access_token);
			$cookie['connect_refresh_code'] = $this->encode($this->refresh_token);
			set_self_cookie($cookie);
	
			$appid = $this->CI->input->get_post('appid');
			$connect_bind_link = $this->CI->config->item('connect_bind_link');
			if (isset($connect_bind_link[$appid]))
			{
				$url = $connect_bind_link[$appid];
			}
			else
			{
				$url = array_shift($connect_bind_link);
			}
			
			if ($this->CI->input->get_post('redirect'))
			{
				$param['redirect'] = urlencode($this->CI->input->get_post('redirect'));
			}
		}
		$url = sso_build_url($url, $param);
		redirect($url);
	}
	
	function auto_bind_connect($userid = '')
	{
		$access_token = $this->access_token ? $this->access_token : $this->decode($this->CI->input->cookie('connect_code'));
		$refresh_token = $this->refresh_token ? $this->refresh_token : $this->decode($this->CI->input->cookie('connect_refresh_code'));
		if ($access_token && $userid)
		{
			$openid = $this->openid ? $this->openid : $this->get_connect_openid($access_token);
			$flag = $this->bind_connect($userid, $openid, $access_token, $refresh_token);
			if ($flag == 0)
			{
				delete_self_cookie(array('connect_code', 'connect_cooperate'));
			}
			return $flag;
		}
		else
		{
			return 7400;
		}
	}
	
	function auto_get_userinfo()
	{
		$access_token = $this->access_token ? $this->access_token : $this->decode($this->CI->input->cookie('connect_code'));
		$res = array('flag'=>7400);
		if ($access_token)
		{
			$result = $this->execute_method('users.getInfo', array('fields'=>'name','access_token'=>$access_token));
			$data = json_decode($result);
			if (is_array($data))
			{
				$userinfo = $data[0];
				if (isset($userinfo->name))
				{
					$res['flag'] = 0;
					$res['nickname'] = $userinfo->name;
				}
			}
		}
		return $res;
	}
	
	function code_get_connect_status()
	{
		if (empty($code))
		{
			$code = $this->CI->input->get_post('code');
		}
		
		$url = $this->get_token_server_url($code);
		$returns = $this->fopen($url);
		$res = json_decode($returns);
		
		if (isset($res->access_token) && isset($res->user))
		{
			$access_token = $this->access_token = $res->access_token;
			$openid = $this->openid = $res->user->id;
			$refresh_token = $this->refresh_token = $res->refresh_token;
			return $this->get_connect_bind_status($openid, $access_token, $refresh_token);
		}
		return 7400;
	}
	
	function auto_get_connect_status()
	{
		$access_token = $this->decode($this->CI->input->cookie('connect_code'));
		if ($access_token)
		{
			$openid = $this->get_connect_openid($access_token);
			if ($openid)
			{
				return $this->get_connect_bind_status($openid, $access_token);
			}
		}
		return 7400;
	}
	
	function get_connect_bind_status($openid = '', $access_token = '', $refresh_token = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		if ( ! empty($openid))
		{
			$res = call_mysql_func('f_is_exist_field', array('renren_connect', 'openid', $openid));
			if ($res == 7402)
			{
				$this->update_token($openid, $access_token, $refresh_token);
			}
			return $res;
		}
		else
		{
			return 7400;
		}
	}
	
	function update_token($openid = '', $access_token = '', $refresh_token = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		$access_token = empty($access_token) ? $this->access_token : $access_token;
		$refresh_token = empty($refresh_token) ? $this->refresh_token : $refresh_token;
		if ($openid)
		{
			if ($access_token)
			{
				call_mysql_func('f_set_field', array($openid, 'renren_connect', 'access_token', $access_token));
			}
			if ($refresh_token)
			{
				call_mysql_func('f_set_field', array($openid, 'renren_connect', 'refresh_token', $refresh_token));
			}
		}
	}
	
	function get_token_server_url($code = '')
	{
		$current_url = (isset($_SERVER['QUERY_STRING']) && ! empty($_SERVER['QUERY_STRING'])) ? current_url().'?'.$_SERVER['QUERY_STRING'] : current_url();
		$current_url = preg_replace('/\&(code|state)=[^\&]+\b/', '', $current_url);
		$token_param = array();
		$token_param[] = $this->_config['token_server'];
		$token_param[] = $this->_config['app_key'];
		$token_param[] = $this->_config['app_secret'];
		$token_param[] = $code;
		$token_param[] = urlencode($current_url);
		return call_user_func_array('sprintf', $token_param);
	}
	
	function bind_connect($userid, $openid = '', $access_token = '', $refresh_token = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		$access_token = empty($access_token) ? $this->access_token : $access_token;
		$refresh_token = empty($refresh_token) ? $this->refresh_token : $refresh_token;
		if ($openid && $access_token)
		{
			if (call_mysql_func('f_user_bind_connect', array('renren', $userid, $openid, $access_token)) == 0)
			{
				call_mysql_func('f_set_field', array($openid, 'renren_connect', 'refresh_token', $refresh_token));
			}
			return 0;
		}
		else
		{
			return 7400;
		}
	}
	
	function get_connect_openid($access_token = '')
	{
		$res = $this->execute_method('users.getInfo', array('fields'=>'uid','access_token'=>$access_token));
		$data = json_decode($res);
		if (is_array($data))
		{
			$userinfo = $data[0];
			if (isset($userinfo->uid))
			{
				$this->openid = $userinfo->uid;
				return $this->openid;
			}
		}
		return false;
	}
	
	function execute_method($method, $params = array())
	{
		$params['api_key']	= $this->_config['app_key'];
		$params['method']	= $method;
		$params['v']		= '1.0';
		$params['format']	= 'json';
		ksort($params);
		reset($params);
		$str = '';
		$post = '';
		foreach($params AS $k=>$v)
		{
			$str .= $k.'='.$v;
			$post.= $k.'='.$v.'&';
		}
		$str = md5($str.$this->_config['app_secret']);
		$post .= 'sig='.$str;
		$url = $this->_config['data_server'].'?'.$post;
		return $this->fopen($url);
	}
	
	function get_user_login_info($openid = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		return call_mysql_pro('p_get_fields', array('user_login', 'renren_connect_openid', $openid), 'row_array');
	}
	
}
