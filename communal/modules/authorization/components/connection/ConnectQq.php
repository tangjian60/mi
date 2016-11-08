<?php
namespace communal\modules\authorization\components\connection;

use Yii;
use communal\modules\authorization\helpers\CookieHelper;

class ConnectQq extends ConnectBase
{
	function auto_bind_connect($userid = '')
	{
		$connect_code = Yii::$app->request->post('connect_code') ? : Yii::$app->request->cookies['connect_code'];
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
			return 7100;
		}
	}
	
	function auto_get_userinfo()
	{
		$connect_code = $this->CI->input->get_post('connect_code') ? $this->CI->input->get_post('connect_code') : $this->CI->input->cookie('connect_code');
		$access_token = $this->access_token ? $this->access_token : $this->decode($connect_code);
		$res = array('flag'=>7100);
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
		if ($access_token = $this->connect_set_token())
		{
			$openid = $this->get_connect_openid($access_token);
			return $this->get_connect_bind_status($openid, $access_token);
		}
		return 7100;
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
		return 7100;
	}
	
	function get_connect_bind_status($openid = '', $access_token = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		$access_token = empty($access_token) ? $this->access_token : $access_token;
		if ( ! empty($openid))
		{
			$res = call_mysql_func('f_is_exist_field', array('qq_connect', 'openid', $openid));
			if ($res == 7102)
			{
				$this->update_token($openid, $access_token);
			}
			return $res;
		}
		else
		{
			return 7100;
		}
	}
	
	function update_token($openid = '', $access_token = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		$access_token = empty($access_token) ? $this->access_token : $access_token;
		if ($openid && $access_token)
		{
			return call_mysql_func('f_set_field', array($openid, 'qq_connect', 'access_token', $access_token));
		}
	}
	
	function bind_connect($userid, $openid = '', $access_token = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		$access_token = empty($access_token) ? $this->access_token : $access_token;
		if ($openid && $access_token)
		{
			return call_mysql_func('f_user_bind_connect', array('qq', $userid, $openid, $access_token));
		}
		else
		{
			return 7100;
		}
	}
	
	function get_connect_openid($access_token = '')
	{
		$server_param = array();
		$server_param[] = $this->_config['data_server'];
		$server_param[] = $access_token;
		$url = call_user_func_array('sprintf', $server_param);
		$response = file_get_contents($url);
		if (strpos($response, "callback") !== false)
		{
			$lpos = strpos($response, "(");
			$rpos = strrpos($response, ")");
			$response  = substr($response, $lpos + 1, $rpos - $lpos -1);
			$msg = json_decode($response);
			if (isset($msg->client_id) && $msg->client_id == $this->_config['app_id'])
			{
				$this->openid = $msg->openid;
				return $msg->openid;
			}
		}
		return false;
	}
	
	function get_user_login_info($openid = '')
	{
		$openid = empty($openid) ? $this->openid : $openid;
		return call_mysql_pro('p_get_fields', array('user_login', 'qq_connect_openid', $openid), 'row_array');
	}
	
	function connect_set_token($code = '')
	{
		if (empty($code))
		{
			$code = $this->CI->input->get_post('code');
		}
		$url = $this->get_token_server_url($code);
		$returns = file_get_contents($url);
		
		if (preg_match('/access_token\=(\w+)\&expires\_in\=(\w+)\b/im', $returns, $match))
		{
			$this->access_token = $match[1];
			$this->expires_in = $match[2];
			return $this->access_token;
		}
		else
		{
			return false;
		}
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
			if (isset($data->ret) && $data->ret == 0 )
			{
				return $data->nickname;
			}
		}
		return false;
	}
	
}
