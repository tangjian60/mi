<?php
/**
 * @desc	图片类
 * @author	lijianwei	2013-3-11
 */
class DF_Image{
	/**
	 * 检测是否是真正的图片
	 */
	public static function checkRealImage($image_path = ''){
		return (FALSE !== @getimagesize($image_path));
	}
	
}