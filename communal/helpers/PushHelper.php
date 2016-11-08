<?php
/**
 * @desc      推送
 */

namespace communal\helpers;

use Yii;
use communal\extensions\push\IGtPush;
use communal\extensions\push\ApnsPush;

class PushHelper
{
    const TYPE_ANDROID    = 1;
    
    const TYPE_IOS        = 2;

    /**
     * @desc    android推送
     * @param   array   $data                    
     * 
     * $data = array(
     *                  'title' => $title, 
     *                  'content' => $title
     *                  'readingid' => json_encode(compact('notice_type', 'msg_id', 'msg_type')), //真实数据
     *                  'sound' => true,
     *                  'android_clientid' => $device['device'],  //设备码
     *         );
     * @param   string  $target  配置分组  从 local.ini 读取对应配置
     * 
     * @return  void
     */
    public static function pushAndroid(array $data, $target)
    {
        $key      = ucfirst($target) . 'AndroidPush';
        $config   = Yii::getConfig( $key );
        $igt_push = new IGtPush($config);
        $igt_push->pushMessageToList($data);
    }
    
    /**
     * @desc    ios推送
     * @param   array   $data       
     * $data = array(
     *                 'token' => $device['device'],
     *                 'alert' => $title,
     *                 'sound' => true,
     *         );
     *         $data = array_merge($data, compact('notice_type', 'msg_id', 'msg_type'));  //真实数据
     * @param   string  $target
     */
    public static function pushiOS(array $data, $target)
    {
        $key     = ucfirst($target) . 'iOSPush';
        $config  = Yii::getConfig( $key );
        $iOSPush = new ApnsPush();

        $iOSPush->applePushUrl = $config['pushUrl'];
        $iOSPush->cert         = $config['certDir'] . DIRECTORY_SEPRETOR .  $config['cert'];
        $iOSPush->passphrase   = $config['passphrase'];
        
        return $iOSPush->send($data);
    }
   
}
