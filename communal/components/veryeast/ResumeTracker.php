<?php

namespace communal\components\veryeast;


use communal\components\BaseComponent;
use communal\components\resource\Task;
use communal\components\resource\WeixinApi;
use communal\models\ve_person\Person as PersonModel;
use communal\models\ve_stat\StatCompanyApplyResumeTrack as Tracker;
use communal\models\ve_stat\UserConnectWeixin;
use yii\base\Exception;
use Yii;

class ResumeTracker extends BaseComponent
{
    /**
     * 投递简历
     *
     * @param $cUserid
     * @param $pUserid
     * @param $jobId
     * @param $jobName
     * @param $companyName
     * @throws \Exception
     * @return bool
     */
    public  function apply($cUserid, $pUserid, $jobId, $jobName, $companyName)
    {
        $cUserid = intval($cUserid);
        $pUserid = intval($pUserid);
        $jobId = intval($jobId);

        if (empty($cUserid) || empty($pUserid) || empty($jobId)) {
            throw new Exception('param error');
        }

        //是否投递过
        $model = Tracker::find()->where([
            'p_userid' => $pUserid,
            'job_id' => $jobId,
        ])->one();
        if (!$model) {
            $model = new Tracker();
            $model->c_userid = $cUserid;
            $model->p_userid = $pUserid;
            $model->job_id = $jobId;
        }

        $model->apply_time = time();
        $model->is_invite = 0;
        $model->invite_time = 0;
        $model->interview_message = '';
        $model->is_view = 0;
        $model->view_time = 0;
        $model->is_refuse = 0;
        $model->refuse_time = 0;
        $model->modify_time = 0;
        $model->progress = 30;
        $model->is_view_user = 0;
        $model->is_click_user = 0;
        if ($model->save()) {
            $person = PersonModel::find()->where([
                'user_id' =>$pUserid
            ])->one();
            $production = array(
                'job_name' => $jobName,
                'company_name' => $companyName,
                'apply_time' => time(),
                'uuid' => $model->uuid,
                'p_userid' => $pUserid,
                'person_name' => $person->true_name_cn ? $person->true_name_cn : $person->true_name_en,
                'type' => 1
            );

            if(!Task::add('ResumeTracker.pushWexin', $production)){
                return $this->SendWeixinMsg($production);
            }
        }

        return false;
    }

    public function SendWeixinMsg($info){
        extract($info);
        if (empty($p_userid) || empty($job_name) || empty($company_name) || empty($type)) {
            throw new Exception('param error');
        }
        $row = UserConnectWeixin::find()->where([
            'userid' => $p_userid,
            'app_name' => 'veryeast'
        ])->one();

        //重试三次
        if($row){
            $i = 0;
            do{
                try{
                    $config = Yii::getConfig('veryeast','@communal/config/weixin/account.php');
                    $weixinApi = Yii::getCommunalComponent('weixinApi',['account' => 'veryeast']);
                    $accessToken = $weixinApi->getAccessToken();
                    $openid = $row['openid'];
                    $requestUrl = sprintf(
                        "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s",
                        $accessToken
                    );
                    $data = $this->getTemplateData($type,$openid,$info);
                    $data = json_encode($data);
                    $result = $this->httpRequest($requestUrl,$data);
                    $result = json_decode($result,true);
                    if ($result['errcode'] != 0) {
                        //清空缓存
                        if($result['errcode'] == 40001) {
                            $this->deleteCache($config['appid'].'_access_token');
                        }

                        throw new Exception("发送失败 errcode: {$result['errcode']}");
                    }

                    return true;
                }catch(Exception $e){
                    $i++;
                }
            }while($i < 3);
        }

    }

    protected function getTemplateData($type, $openid, $info)
    {
        extract($info);
        $firstMessage = array(
            1 => "%s，你好~\r\nHR已收到您的简历\r\n请点击查看流程",
            2 => "%s，你好~\r\nHR已查看您的简历\r\n请点击查看流程",
            3 => "%s，你好~\r\n%s邀请您面试啦！\r\n请点击查看流程",
            4 => "%s，你好~\r\n您的条件暂不符合本公司要求，无法进入面试。",
            5 => "%s，你好~\r\n您的简历已被下载。"
        );

        //模板id
        if ($type == 3) {
            $template_id = 'K8ucr2Cc5iRKfsRhfIZzUlbE8-DE4Y6_psLTcAyZJBs';
        } else {
            $template_id = 'hHPbwGKZ4WRNoFZrVegvBLdv8QJnD8th4502xym_IRE';
        }

        //标语
        $first = '';
        if (isset($firstMessage[$type])) {
            if ($type == 3) {
                $first = sprintf($firstMessage[$type], $person_name, $company_name);
            } else {
                $first = sprintf($firstMessage[$type], $person_name);
            }
        }

        //数据结构
        $data = array(
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => 'http://m.veryeast.cn/person/trackerDetail?uuid='.$uuid, //todo
            'topcolor' => '#000000',
            'data' =>
                array(
                    'first' =>
                        array(
                            'value' => $first,
                            'color' => '#0A0A0A',
                        ),
                    'job' =>
                        array(
                            'value' => $job_name, //职位名
                            'color' => '#CCCCCC',
                        ),
                    'company' =>
                        array(
                            'value' => $company_name, //公司名
                            'color' => '#CCCCCC',
                        ),
                    'time' =>
                        array(
                            'value' => date('m月d日 H:i', $apply_time), //投递时间
                            'color' => '#CCCCCC',
                        )
                ),
        );

        //面试
        if ($type == 3) {
            $invite = array(
                'time' => array(
                    'value' => $invite_time, //面试时间
                    'color' => '#cccccc', //面试时间
                ),
                'address' => array(
                    'value' => $invite_address, //面试地点
                    'color' => '#cccccc', //面试时间
                ),
                'contact' => array(
                    'value' => $invite_contacts, //联系人
                    'color' => '#cccccc', //面试时间
                ),
                'tel' => array(
                    'value' => $invite_phone, //联系电话
                )
            );
            $data['data'] = array_merge($data['data'], $invite);
        }

        return $data;
    }
    /**
     * http请求
     *
     * @param $requestUrl
     * @param $data
     * @return mixed
     */
    protected  function httpRequest($requestUrl, $data)
    {
        $curl = curl_init($requestUrl);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }

}