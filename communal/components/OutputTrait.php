<?php

namespace communal\components;

use Yii;
use yii\web\Response;
/**
 * Controller trait
 */
trait OutputTrait
{
    /**
     * 输出Ajax数据内容
     * @param  array  $data 数据内容
     * @return void
     */
    public function ajaxOutput(array $data = array())
    {
        $data = array(
            'status' => 1,
            'data'  => $data
        );

        $this->apiRender($data);
    }


    /**
     * 输出Ajax错误格式
     * @param  int $errCode 错误代码
     * @param  string $errMsg  错误信息
     * @return void
     */
    public function ajaxError($errCode, $errMsg)
    {
        $data = array(
            'status'   => 0,
            'errCode' => $errCode,
            'errMsg'  => $errMsg
        );

        $this->apiRender($data);
    }


    /**
     * api方式返回数据
     * @param array $data
     */
    public function apiRender(array $data)
    {
        $request = Yii::$app->request;
        $response = Yii::$app->response;

        $format = $request->get('format');
        $callback = $request->get('callback');
        $domain   = $request->get('domain');

        switch ($format)
        {
            case 'iframe':
                $response->format = Response::FORMAT_RAW;
                $str = "<script type=\"text/javascript\">\n";
                $str .= $domain ? "document.domain = '" . $domain . ";\n" : '';
                $str .= $callback . "(" . json_encode($data) . ");\n";
                $str .= "</script>";
                $response->data = $str;
                break;
            case 'jsonp' :
                $response->format = Response::FORMAT_JSONP;
                $response->data['callback'] = $callback;
                $response->data['data'] = $data;
                break;
            case 'json':
            default:
                $response->format = Response::FORMAT_JSON;
                $response->data = $data;
                break;
        }

        $response->send();
        exit;
    }
}
