<?php
namespace common\extensions\weibo;
use common\extensions\utils\Curl;
/**
 * @desc	微博接口
 * @author	lijianwei	2013-2-27
 */
class Weibo_Api{
	/**
	 * 根据微博昵称获取用户uid 
	 * @param string $nickname
	 * @return int 
	 */
	public static function getUidByNick($nickname = ''){
		$url = 'http://open.weibo.com/widget/ajax_getuidnick.php';
		$param = array('nickname' => $nickname);
		$curl_opt_arr = array(
			CURLOPT_REFERER => 'http://open.weibo.com/widget/followbutton.php',
			CURLOPT_HTTPHEADER => array('Host' => 'open.weibo.com'),
			CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; rv:19.0) Gecko/20100101 Firefox/19.0',
			CURLOPT_COOKIE => 'SINAGLOBAL=4366890993724.4136.1358920982289; UOR=ent.ifeng.com,widget.weibo.com,#www.baidu.com; ULV=1363074063910:11:4:2:9804603678088.016.1363074063890:1363064995944; __utma=15428400.50825375.1359770726.1362385045.1362984497.8; __utmz=15428400.1362984497.8.8.utmcsr=blog.sina.com.cn|utmccn=(referral)|utmcmd=referral|utmcct=/s/blog_7be8a2150100qkrp.html; ssoln=lijianwei_123%40126.com; _s_tentry=www.veryeast.cn; Apache=9804603678088.016.1363074063890; USRHAWB=usrmdins21370',
		);

		$json_str = Curl::curl_post_content($url, $param, $curl_opt_arr);
		
		if(!empty($json_str)){
			$json_arr = json_decode($json_str, TRUE);
			return isset($json_arr['data']) ? intval($json_arr['data']) : 0;
		}
		return 0;
	}
}