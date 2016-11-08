<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once dirname(__FILE__).'/Connect_base.php';
class Connect_weibo extends Connect_base
{
	function auto_bind_connect($userid = '')
	{
		$connect_code = $this->CI->input->get_post('connect_code') ? $this->CI->input->get_post('connect_code') : $this->CI->input->cookie('connect_code');
		$access_token = $this->access_token ? $this->access_token : $this->decode($connect_code);
		if ($access_token && $userid)
		{
			$openid = $this->openid ? $this->openid : $this->get_connect_openid($access_token);
			
			$flag = $this->bind_connect($userid, $openid, $access_token);
			if ($flag == 0)
			{
				delete_self_cookie(array('connect_code', 'connect_cooperate'));
			}
			return $flag;
		}
		else
		{
			return 7200;
		}
	}
	
	function auto_get_userinfo()
	{
		$connect_code = $this->CI->input->get_post('connect_code') ? $this->CI->input->get_post('connect_code') : $this->CI->input->cookie('connect_code');
		$access_token = $this->access_token ? $this->access_token : $this->decode($connect_code);
		$res = array('flag'=>7200);
		if ($access_token)
		{
			$openid = $this->openid ? $this->openid : $this->get_connect_openid($access_token);
			if ($openid)
			{
				$nickname = $this->get_nickname($openid, $access_token);
				if ( ! empty($nickname))
				{
					$res['flag'] = 0;
					$res['nickname'] = $nickname;
				}
			}
		}
		return $res;
	}
	
	function code_get_connect_status()
	{
		$code = $this->CI->input->get_post('code');
		$url = $this->get_token_server_url($code);
		$returns = $this->fopen($url);
		$res = json_decode($returns);
		if (isset($res->access_token))
		{
			$access_token = $this->access_token = $res->access_token;
			$this->expires_in = $res->expires_in;
			$openid = $this->openid = $res->uid;
			return $this->get_connect_bind_status($openid, $access_token);
		}
		else
		{
			return 7200;
		}
	}
	
	function auto_get_connect_status()
	{
		$connect_code = $this->CI->input->get_post('connect_code') ? $this->CI->input->get_post('connect_code') : $this->CI->input->cookie('connect_code');
		$access_token = $this->decode($connect_code);
		if ($access_token)
		{
			$openid = $this->get_connect_openid($access_token);
			if ($openid)
			{
				return $this->get_connect_bind_status($openid, $access_token);
			}
		}
		return 7200;
	}
	
	function get_connect_bind_status($openid = '', $access_token = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		$access_token = empty($access_token) ? $this->access_token : $access_token;
		if ( ! empty($openid))
		{
			$res = call_mysql_func('f_is_exist_field', array('weibo_connect', 'openid', $openid));
			if ($res == 7202)
			{
				$this->update_token($openid, $access_token);
			}
			return $res;
		}
		else
		{
			return 7200;
		}
	}
	
	function update_token($openid = '', $access_token = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		$access_token = empty($access_token) ? $this->access_token : $access_token;
		if ($openid && $access_token)
		{
			return call_mysql_func('f_set_field', array($openid, 'weibo_connect', 'access_token', $access_token));
		}
	}
	
	function bind_connect($userid, $openid = '', $access_token = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		$access_token = empty($access_token) ? $this->access_token : $access_token;
		if ($openid && $access_token)
		{
			return call_mysql_func('f_user_bind_connect', array('weibo', $userid, $openid, $access_token));
		}
		else
		{
			return 7200;
		}
	}
	
	function get_connect_openid($access_token = '')
	{
		$server_param = array();
		$server_param[] = $this->_config['data_server'];
		$server_param[] = $access_token;
		$url = call_user_func_array('sprintf', $server_param);
		
		$response = $this->fopen($url);
		$res = json_decode($response);
		
		if (isset($res->uid) && isset($res->appkey) && ($res->appkey == $this->_config['app_id']))
		{
			$this->openid = $res->uid;
			return $this->openid;
		}
		return false;
	}
	
	function get_user_login_info($openid = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		return call_mysql_pro('p_get_fields', array('user_login', 'weibo_connect_openid', $openid), 'row_array');
	}
	
	function get_nickname($openid = '', $access_token = '')
	{
		if ($openid && $access_token)
		{
			$server_param = array();
			$server_param[] = $this->_config['user_info_server'];
			$server_param[] = $access_token;
			$server_param[] = $openid;
			$url = call_user_func_array('sprintf', $server_param);
			$response = file_get_contents($url);
			$data = json_decode($response);
			if (isset($data->name))
			{
				return $data->name;
			}
		}
		return false;
	}
	
}
