<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once dirname(__FILE__).'/Connect_base.php';
class Connect_163 extends Connect_base
{
	public $refresh_token;
	
	function auto_bind_connect($userid = '')
	{
		$access_token = $this->access_token ? $this->access_token : $this->decode($this->CI->input->cookie('connect_code'));
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
			return 7300;
		}
	}
	
	function auto_get_userinfo()
	{
		$access_token = $this->access_token ? $this->access_token : $this->decode($this->CI->input->cookie('connect_code'));
		$res = array('flag'=>7300);
		
		$data = $this->get_userinfo_by_token($access_token, array('name', 'email'));
		if ( ! empty($data['name']) || ! empty($data['email']))
		{
			$res['flag'] = 0;
			$res['nickname'] = $data['name'];
			$res['email'] = $data['email'];
		}
		return $res;
	}
	
	function code_get_connect_status($code = '')
	{
		if (empty($code))
		{
			$code = $this->CI->input->get_post('code');
		}
		$url = $this->get_token_server_url($code);
		$returns = file_get_contents($url);
		$res = json_decode($returns);
		if (isset($res->access_token) && isset($res->uid))
		{
			$access_token = $this->access_token = $res->access_token;
			$openid = $this->openid = $res->uid;
			$refresh_token = $this->refresh_token = $res->refresh_token;
			$this->expires_in = $res->expires_in;
			return $this->get_connect_bind_status($openid, $access_token, $refresh_token);
		}
		return 7300;
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
		return 7300;
	}
	
	function get_connect_bind_status($openid = '', $access_token = '',  $refresh_token='')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		if ( ! empty($openid))
		{
			$res = call_mysql_func('f_is_exist_field', array('163_connect', 'openid', $openid));
			if ($res == 7302)
			{
				$this->update_token($openid, $access_token, $refresh_token);
			}
			return $res;
		}
		else
		{
			return 7300;
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
				call_mysql_func('f_set_field', array($openid, '163_connect', 'access_token', $access_token));
			}
			if ($refresh_token)
			{
				call_mysql_func('f_set_field', array($openid, '163_connect', 'refresh_token', $refresh_token));
			}
		}
	}
	
	function bind_connect($userid, $openid = '', $access_token = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		$access_token = empty($access_token) ? $this->access_token : $access_token;
		if ($openid && $access_token)
		{
			return call_mysql_func('f_user_bind_connect', array('163', $userid, $openid, $access_token));
		}
		else
		{
			return 7300;
		}
	}
	
	function get_connect_openid($access_token = '')
	{
		$res = $this->get_userinfo_by_token($access_token, 'id');
		if ( ! empty($res['id']) )
		{
			$this->openid = $res['id'];
			return $this->openid;
		}
		return false;
	}
	
	function get_userinfo_by_token($access_token = '', $params = array('id'))
	{
		$server_param = array();
		$server_param[] = $this->_config['data_server'];
		$server_param[] = $access_token;
		$url = call_user_func_array('sprintf', $server_param);
		$response = file_get_contents($url);
		$res = json_decode($response);
		$return = array();
		if ( ! is_array($params))
		{
			$params = array($params);
		}
		foreach ($params as $value)
		{
			$return[$value] = isset($res->$value) ? $res->$value : '';
		}
		return $return;
	}
	
	function get_user_login_info($openid = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		return call_mysql_pro('p_get_fields', array('user_login', '163_connect_openid', $openid), 'row_array');
	}
	
	function get_nickname($openid = '', $access_token = '')
	{
		if ($openid && $access_token)
		{
			$server_param = array();
			$server_param[] = $this->_config['user_info_server'];
			$server_param[] = $access_token;
			$server_param[] = $this->_config['app_id'];
			$server_param[] = $openid;
			$server_param[] = 'json';
			$url = call_user_func_array('sprintf', $server_param);
			$response = file_get_contents($url);
			$data = json_decode($response);
			if (isset($data->ret) && $data->ret == 0)
			{
				return $data->nickname;
			}
		}
		return false;
	}
	
}