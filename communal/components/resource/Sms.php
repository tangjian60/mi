<?php

namespace communal\components\resource;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use communal\components\BaseComponent;
use communal\models\mssql_jdrc\SMSSend;
use communal\models\DfResource;

/**
 * @desc 短信发送
 */
class Sms extends BaseComponent
{
    //mem key
    private  $cacheKey = 'communal\\components\\resource\\Sms.sms_sensitive_word';
    //找到的敏感词
    public  $occurWords = array();

    /***
     * 发送短信
     *
     * @param string $mobile  手机号码
     * @param string $content 短信内容
     *
     * @throws \common\extensions\storage\Exception
     * @return bool
     */
    public function sendSms($mobile = '', $content = '')
    {
        if(!$mobile || !$content){
            throw new Exception('参数错误');
        }

        if(!preg_match('/^\d{11}$/', $mobile)){
            throw new Exception('手机号码格式不正确');
        }
        
        //过滤下
        $content = $this->filter($content);
        
        //加上短信签名
        $content = $this->addSmsSign($content);
        
        return $this->send($mobile, $content);
    }

    /** 插入短息队列
     * @param string $mobile 手机号码
     * @param string $content 短信内容
     * @return bool 是否发送成功
     * @throws Exception 参数错误
     */
    protected function send($mobile = '', $content = '')
    {
        $model = new SMSSend;
        $model->Mobile = $mobile;
        $model->Content = $content;
        return $model->save();
    }
    
    protected function addSmsSign($content)
    {
        if (preg_match('/(最佳东方[^】]|veryeast)/i', $content)) {
            $content = rtrim($content, '【最佳东方】'). "【最佳东方】";
        } else if (preg_match('/(先之[^】]|9first)/i', $content)) {
            $content = rtrim($content, '【先之】'). "【先之】";
        }

	   return $content;
    }

    /**
     * 
     * 敏感词
     * @param string $sms_content 短信内容
     * @param int $filter_callback  处理方法   1 字符中间加空格  2 过滤掉
     * @return string
     */
    public function filter($sms_content = '', $filter_callback = 1)
    {
        $sms_content = trim($sms_content);

        if($filter_callback == 1)
            $callback = 'addSpace';
        else
            $callback = 'strip';
        
        $word_data = [];
        $this->getWordData($word_data);
        foreach($word_data as $word) {
            if( false !== strpos($sms_content, "$word") ) {
                $this->occurWords[] = $word;
                $sms_content = str_replace($word, call_user_func_array([$this, $callback], [$word, $sms_content]), $sms_content);
            }   
        }
        return $sms_content;
    }
    
    //获取敏感词
    protected function getWordData(array &$word_data = [])
    {
        if(!$word_data = parent::getCache($this->cacheKey)) {

            $data = (new Query)
                ->select('word')
                ->from('{{sms_sensitive_word}}')
                ->where('enabled = 1')
                ->all( DfResource::getDb() );

            foreach($data as $k => $v) {
                $word_data[] = $v['word'];
            }
    
            parent::setCache($this->cacheKey, $word_data);
        }
    }
    
    //callback 1
    protected function addSpace($word, $sms_content)
    {
        return implode(' ', preg_split('/(?<!^)(?!$)/u', $word));
    }
    //callback  2
    protected function strip($word, $sms_content)
    {
        return '';
    }
    
}