<?php
namespace common\extensions\utils;
/**
 * @desc	图片处理类
 * @author	lijianwei	2013-3-7
 */
class Image{
	/**
	 * 仅生成缩略图
	 * @param string $pic 原图路径
	 * @param int $newwidth 生成的宽度
	 * @param int $newheight 生成的高度
	 * @param int $savepath  缩略图保存的文件路径
	 * @return boolean or string
	 */
	public function image_resize($pic,$newwidth=100,$newheight=100,$savepath=''){
		if(!is_file($pic)) return FALSE;
		if(!is_dir(dirname($savepath))) return FALSE;
		
		$ext = strtolower(pathinfo($savepath, PATHINFO_EXTENSION));
		if($ext=='jpg'||$ext=='jpeg'){
			$img=@imagecreatefromjpeg($pic);
		}else if($ext=='png'){
			$img=@imagecreatefrompng($pic);
		}else if($ext=='gif'){
			$img=@imagecreatefromgif($pic);
		}else if($ext=='bmp'){
			$img=@imagecreatefromwbmp($pic);
		}

		if($img){
			$width=imagesx($img);
			$height=imagesy($img);
			$tmp_img=imagecreatetruecolor($newwidth,$newheight);
			imagecopyresampled($tmp_img,$img,0,0,0,0,$newwidth,$newheight,$width,$height); //imagecopyresized  方法较快，但质量不好
			
			switch($ext){
				case 'jpg':
				case 'jpeg':
					imagejpeg($tmp_img,$savepath);
					break;
				case 'gif':
					imagegif($tmp_img,$savepath);
					break;
				case 'png':
					imagepng($tmp_img,$savepath);
					break;
				case 'bmp':
					imagewbmp($tmp_img,$savepath);
					break;

			}
			imagedestroy($img);
			imagedestroy($tmp_img);
			return $savepath;
		}
		return FALSE;
	}
	//图片预验证   仅支持单个图片上传
	public function pre_validate_img_upload($field = ''){
		if($_FILES[$field]['error'] != 0){
			return '上传过程出现错误';
		}
		if(!Image::check_real_img($_FILES[$field]['tmp_name'])){
			return '请上传图片';
		}
		if($_FILES[$field]['size'] > 4*1024*1024){
			return '请上传小于4M的文件';
		}
		$ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
		if(!in_array($ext, array('jpeg', 'jpg', 'png', 'gif', 'bmp'))){
			return '请上传jpeg,jpg,png,gif,bmp类型的文件';
		}
		return 0;
	}
	//获取图片的宽度和高度   仅支持单个图片上传
	public function getImageSize($field = ''){
		if(isset($_FILES[$field]['tmp_name']) && $_FILES[$field]['tmp_name']){
			list($width, $height) = getimagesize($_FILES[$field]['tmp_name']);
			return array($width, $height);
		}
		return array();
	}
	//检测是否真正的图片
	public static function check_real_img($img_path = ''){
		return @getimagesize($img_path);
	}
	//上传图片
	public function uploadPic($field = '', $savepath = ''){
		if(is_uploaded_file($tmp_name = $_FILES[$field]['tmp_name'])){
			move_uploaded_file($tmp_name, $savepath);
			return $savepath;
		}
		return '';
	}
}