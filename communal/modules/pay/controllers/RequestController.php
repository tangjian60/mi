<?php

namespace communal\modules\pay\controllers;

use Yii;
use yii\web\HttpException;
use communal\modules\pay\components\PayController;
use communal\modules\pay\models\OrderForm;
use communal\helpers\StringHelper;

class RequestController extends PayController
{
	public $enableCsrfValidation = false;
	public $defaultAction	= 'submit';
	
	public function actionSubmit()
	{
		$form = new OrderForm();
		$form->attributes = $_REQUEST;
	
		if( isset($_REQUEST['code']) ){
			$form->payid = 3;
			$form->pay_bank = 'weixin';
		}
		
		$payComponent = $this->module->basePay;
		
		if ($form->validate())
		{
			$orderModel = $payComponent->getOrderModel($form->order_id);
			$params = $form->attributes;
			if ( ! $orderModel) throw new HttpException(500, '订单不存在或已删除！');
			
			switch ($params['payid'])
			{
				case 1:
					{
						$alipayComponent	= $this->module->getPayComponent('alipay');
						$parameter			= $this->initAlipayParams($orderModel, $params);
						$form				= $alipayComponent->submitForm($parameter);
						$html				= $this->buildFormHtml($form, 'UTF-8', $payComponent->payTitle, $payComponent->payTips);
					}
					break;
				case 2:
					{
						$yeepayComponent	= $this->module->getPayComponent('yeepay');
						$parameter			= $this->initYeepayParams($orderModel, $params);//Yii::p($yeepayComponent);
						$form				= $yeepayComponent->submitForm($parameter);
						$html				= $this->buildFormHtml($form, 'GBK', $payComponent->payTitle, $payComponent->payTips);
					}
					break;
				case 3:
					{
						$weixinComponent = $this->module->getPayComponent('weixin');
						$parameter = $this->initWeixinParams($orderModel);
						$jsApiParameters = $weixinComponent->initWxpayParams($parameter);
						$redirect = $payComponent->success_redirect;

						echo $this->renderPartial('weixin_submit', compact('jsApiParameters', 'redirect'));
						Yii::$app->end();
					}
					break;
				case 11:
					{
						$wapAlipayComponent = $this->module->getPayComponent('wap_alipay');
						$parameter	= $this->initAlipayParams($orderModel, $params);
						
						$form	= $wapAlipayComponent->wapSubmitForm($parameter);
						$html	= $this->buildFormHtml($form, 'UTF-8', $payComponent->payTitle, $payComponent->payTips);
					}
					break;
				case 21:
				    {
				        $clientAlipayComponent = $this->module->getPayComponent('client_alipay');
				        $parameter	= $this->initAlipayParams($orderModel, $params);
				        
				        $params = $clientAlipayComponent->clientSubmitParam($parameter);
				        
				        $arr = array();
				        foreach ($params as $key => $val) {
				            $arr[] = $key . '=' . $val;
				        }
				        
				        echo json_encode(array(
				            'status' => 1,
				            'data' => array('params' => implode($arr, '&'))
				        ));
				        
				        exit;
				    }
				    break;
				default:
					break;
			}
			
			echo $html;
			exit;//Yii::$app->end();
		}
		else
		{	
			throw new HttpException(500,'订单不存在或已删除！');
		}
	}
	
	/**
	 * 构建表单提交页面html
	 *
	 * @param string $form
	 * @param string $charset
	 * @param string $title
	 * @param string $tips
	 *
	 * @return string $html	页面html
	 */
	public function buildFormHtml($form = '', $charset = 'UTF-8', $title = '', $tips = '')
	{
		$html = '<html>';
		$html .= '<head>';
		$html .= '<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'">';
		$html .= '<title>'.$title.'</title>';
		$html .= '<body>';
		$html .= $tips.'';
		$html .= $form.'';
		$html .= '</body>';
		$html .= '</html>';
		if (strtolower($charset) != 'utf-8')
		{
			$html = StringHelper::iconv('UTF-8', $charset, $html);
		}
		
		return $html;
	}
	
	/**
	 * 初始化支付宝提交参数
	 *
	 * @param string $orderId
	 * @param array $params
	 * @return array
	 */
	protected function initAlipayParams($orderModel, array $params = array())
	{
		$alipayParam = array();
		if (isset($params['pay_bank']) && ! empty($params['pay_bank']))
		{
			$alipayParam['paymethod']	= 'bankPay';
			$alipayParam['defaultbank']	= $params['pay_bank'];
		}
		
		$alipayParam['out_trade_no']= $orderModel->order_id;
		$alipayParam['subject']		= $orderModel->order_title;
		$alipayParam['total_fee']	= $orderModel->pay_amount;
		$alipayParam['body']		= $orderModel->order_detail ? $orderModel->order_detail : $orderModel->order_title;
		
		return $alipayParam;
	}
	
	/**
	 * 初始化易宝提交参数
	 *
	 * @param string $orderId
	 * @param array $params
	 * @return array
	 */
	protected function initYeepayParams($orderModel, array $params = array())
	{
		$yeepayParam = array();
		$yeepayParam['order']	= $orderModel->order_id;
		$yeepayParam['pid']		= $orderModel->order_title;
		$yeepayParam['pcat']	= $orderModel->order_title;
		$yeepayParam['pdesc']	= $orderModel->order_detail ? $orderModel->order_detail : $orderModel->order_title;
		$yeepayParam['amt']		= $orderModel->pay_amount;
		$yeepayParam['frpId']	= isset($params['pay_bank']) ? $params['pay_bank'] : '';

		return $yeepayParam;
	}

	/**
	 * 初始化微信提交参数
	 * @param Object  $orderModel
	 */
	protected function initWeixinParams($orderModel){
		$openid = Yii::getCommunalComponent('weixinApi', ['account' => 'veryeast'])->openid;
		
		$params = array();
		$params['openid']	    = $openid;
		$params['body']	        = $orderModel->order_title;
		$params['out_trade_no']	= $orderModel->order_id;
		$params['total_fee']	= intval($orderModel->pay_amount*100);

		return $params;
	}
}