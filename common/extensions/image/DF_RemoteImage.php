<?php
//使用示例
//include 'DF_RemoteImage.php';
//$df_image = new DF_RemoteImage(__DIR__. '/scp.sh');
//$local_file = __DIR__. '/test.jpg';
//$remote_file_path = '/home/lijianwei/test.jpg';
//$df_image->remote_scp($local_file, $remote_file_path);


/**
 * @desc	拷贝图片到图片服务器
 * @author	lijianwei	2013-3-11
 */
class DF_RemoteImage{
	private $_scp_sh_path = 'scp.sh';
	//图片服务器ip
	private $_img_server_ip = '192.168.106.130'; 
	//图片服务器用户名
	private $_img_server_user = 'lijianwei';
		
	
	public function __construct($scp_sh_path = '', $img_server_ip = '', $img_server_user = ''){
		if(!empty($scp_sh_path)) $this->_scp_sh_path = $scp_sh_path;
		if(!empty($img_server_ip)) $this->_img_server_ip = $img_server_ip;
		if(!empty($img_server_user)) $this->_img_server_user = $img_server_user;
	}
	/**
	 * 拷贝图片文件
	 * @param string $local_file_path
	 * @param string $remote_file_path
	 * @param int $is_override  是否覆盖 默认覆盖
	 * @return TRUE OR  FALSE
	 */
	public function remote_scp($local_file_path, $remote_file_path, $is_override = TRUE){
		//如果文件不存在
		if(!is_file($local_file_path)) return FALSE;
		require_once('DF_Image.php');
		//如果不是图片
		if(!DF_Image::checkRealImage($local_file_path)) return FALSE;
		
		//拷贝文件
		@exec("sh {$this->_scp_sh_path} {$this->_img_server_ip} {$this->_img_server_user} {$local_file_path} {$remote_file_path} {$is_override}", $output, $return_var);
		return !$return_var;
	}
}