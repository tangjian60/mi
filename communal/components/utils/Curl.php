<?php


class Curl
{
    private static $_timeout = 30;
    private static $_display_error = true;
    public static function setTimeOut($timeout = 0)
    {
        self::$_timeout = $timeout;
    }
    public static function setDisplayError($display_error)
    {
        self::$_display_error = $display_error;
    }
    //curl get请求
    public static function curl_get_content($url = "", array $curl_opt_arr = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$_timeout);

        if($curl_opt_arr){
            foreach($curl_opt_arr as $opt_name => $opt_value){
                curl_setopt($ch, $opt_name, $opt_value);
            }
        }

        //https
        if(strpos($url, 'https') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
        }

        $return_data = curl_exec($ch);
        if(curl_errno($ch)){
            $msg = "curl url: $url occur error, error msg:". curl_error($ch). PHP_EOL;
            self::$_display_error ? trigger_error($msg) : '';
        }
        curl_close($ch);
        return $return_data;
    }
    //curl post请求
    public static function curl_post_content($url = "", array $param = array(), array $curl_opt_arr = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$_timeout);
        if($curl_opt_arr){
            foreach($curl_opt_arr as $opt_name => $opt_value){
                curl_setopt($ch, $opt_name, $opt_value);
            }
        }
        $return_data = curl_exec($ch);
        if(curl_errno($ch)){
            $msg = "curl url: $url occur error, error msg:". curl_error($ch). PHP_EOL;
            self::$_display_error ? trigger_error($msg) : '';
        }
        curl_close($ch);
        return $return_data;
    }
    //file_get_contents 获取内容
    public static  function file_get_content($url = "")
    {
        $ctx = stream_context_create(array(
            'http' => array(
                'timeout' => self::$_timeout
            )
        ));

        return file_get_contents($url, 0, $ctx);
    }
}