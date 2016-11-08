<?php
namespace communal\modules\authorization\components\connection;

abstract class ConnectBase{
	
	public $_config;
	protected $access_token = '';
	protected $expires_in = 0;
	protected $openid;

	function initialize($params = array())
	{
		$this->_config = $params;
	}
	
	abstract function code_get_connect_status();	//code获取绑定状态
	
	abstract function get_user_login_info();		//获取已绑定用户登录信息
	
	abstract function auto_bind_connect();			//绑定用户
	
	abstract function auto_get_userinfo();			//绑定注册时获取用户信息 昵称 邮箱等
	
	function redirect_login($redirect = '', $state = 'veryeast')
	{
		$param_arr = array();
		$param_arr[] = $this->_config['code_server'];
		$param_arr[] = $this->_config['app_id'];
		$param_arr[] = urlencode($redirect);
		$param_arr[] = $state;
		$url = call_user_func_array('sprintf', $param_arr);
		respond_request('get', $this->_config['scope'], $url);
	}
	
	function redirect_bind($status = '')
	{
		$param = array();
		if (preg_match('/^\d{3}0$/', $status))
		{
			$url = site_url('connect/link/'.$this->_config['cooperate']);
			redirect($url);
		}
		else
		{
			$return_type = $this->CI->input->get_post('return_type');
			$cookie['connect_cooperate'] = $this->_config['cooperate'];
			$cookie['connect_code'] = $this->encode($this->access_token);
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
			
			switch ($this->CI->input->get_post('target')) {
				case 'touch':
					$touch_maps = $this->CI->config->item('touch_connect_bind_link');
					if (isset($touch_maps[$appid])) $url = $touch_maps[$appid];
					break;
			}
			
			if ($this->CI->input->get_post('redirect'))
			{
				$param['redirect'] = urlencode($this->CI->input->get_post('redirect'));
			}
			
			if ($return_type == 'json') {
				$param['flag'] = $status;
				respond_request('json', array_merge($param, $cookie));
			} else {
				$url = sso_build_url($url, $param);
				redirect($url);
			}
		}
	}
	
	function get_token_server_url($code = '')
	{
		$current_url = (isset($_SERVER['QUERY_STRING']) && ! empty($_SERVER['QUERY_STRING'])) ? current_url().'?'.$_SERVER['QUERY_STRING'] : current_url();
		$token_param = array();
		$token_param[] = $this->_config['token_server'];
		$token_param[] = $this->_config['app_id'];
		$token_param[] = $this->_config['app_secret'];
		$token_param[] = $code;
		$token_param[] = urlencode($current_url);
		return call_user_func_array('sprintf', $token_param);
	}
	
	function decode($str = '')
	{
		return $this->str_rev($str);
	}
	
	function encode($str = '')
	{
		return $this->str_rev($str);
	}
	
	function str_rev($str = '')
	{
		return join('', array_reverse(preg_split('//u', $str)));
	}
	
	function fopen($url, $method = 'post')
	{
		if (strpos($url, '?') !== false && $method == 'post')
		{
			list($url, $post) = explode('?', $url);
			if ( ! function_exists('http_fopen'))
			{
				$this->CI->load->helper('socket_helper');
			}
			$returns = http_fopen($url, $post);
		}
		else
		{
			$returns = file_get_contents($url);
		}
		return $returns;
	}
}

