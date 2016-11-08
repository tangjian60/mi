<?php

namespace communal\modules\pay\controllers;

use Yii;
use communal\modules\pay\components\PayController;
use communal\models\df_pay\AlipayTradeLog;
use communal\models\df_pay\YeepayTradeLog;
use communal\helpers\StringHelper;


class RespondController extends PayController
{
	public $enableCsrfValidation = false;

	/**
	 * 支付宝同步回调地址
	 */
	function actionAlipayReturn()
	{	
		$orderId = Yii::$app->request->get('out_trade_no', '');
		
		if ( ! empty($orderId))
		{
			$payComponent = $this->module->basePay;
			//获取支付宝组件
			$alipayComponent = $this->module->getPayComponent('alipay');
			//验证支付参数
			if ($alipayComponent->verifyReturn())
			{
				//检查交易状态
				$status = Yii::$app->request->get('trade_status');
				if($status == 'TRADE_FINISHED' || $status == 'TRADE_SUCCESS')
				{
					//获取支付金额
					$total_fee = Yii::$app->request->get('total_fee');
					//处理订单为支付成功 已处理过订单会返回false
					if ($payComponent->orderPaySuccess($orderId, $total_fee))
					{
						//执行订单逻辑程序，完成订单
						$payComponent->completeOrder(array('order_id' => $orderId, 'payid' => 1));
						//记录支付宝支付记录
						AlipayTradeLog::recordPay($_REQUEST);
					}
				}
				else
				{
					//记录支付宝交易状态异常
					$payComponent->logError($orderId, '支付宝同步trade_status异常！trade_status:'.$_GET['trade_status'], 1);
				}
			}
			
			$payComponent->showReturn(array('order' => $orderId, 'controller' => &$this, 'type' => 'alipay'));
		}
	}
	
	/**
	 * 支付宝异步回调地址
	 */
	function actionAlipayNotify()
	{
		$orderId = Yii::$app->request->post('out_trade_no', '');
		
		if ( ! empty($orderId))
		{
			$payComponent = $this->module->basePay;
			$alipayComponent = $this->module->getPayComponent('alipay');
			
			if ($alipayComponent->verifyNotify())
			{
				$total_fee = Yii::$app->request->post('total_fee');
				if ($payComponent->orderPaySuccess($orderId, $total_fee))
				{
					$payComponent->completeOrder(array('order_id' => $orderId, 'payid' => 1));
					AlipayTradeLog::recordPay($_POST);
				}
			}
		}
	}
	
	/**
	 * 支付宝wap同步回调地址
	 */
	public function actionWapAlipayReturn()
	{
		$orderId = Yii::$app->request->get('out_trade_no', '');
		
		if ( ! empty($orderId))
		{
			$payComponent = $this->module->basePay;
			//获取支付宝组件
			$alipayComponent = $this->module->getPayComponent('wap_alipay');
			//验证支付参数
			if ($alipayComponent->verifyReturn())
			{
				//处理订单为支付成功 已处理过订单会返回false
				if ($payComponent->orderPaySuccess($orderId, NULL, FALSE))
				{
					//执行订单逻辑程序，完成订单
					$payComponent->completeOrder(array('order_id' => $orderId, 'payid' => 11));
					//记录支付宝支付记录
					AlipayTradeLog::recordPay($_REQUEST);
				}
			}
			
			$payComponent->showReturn(array('order' => $orderId, 'controller' => &$this, 'type' => 'wapAlipay'));
		}
	}
	
	/**
	 * 支付宝wap异步验证
	 */
	public function actionWapAlipayNotify()
	{       
		$payComponent = $this->module->basePay;
		$alipayComponent = $this->module->getPayComponent('wap_alipay');
		
		if ( $alipayComponent->verifyNotify()) {//验证成功
			
			//解密（如果是RSA签名需要解密，如果是MD5签名则下面一行清注释掉）
			$alipayNotify = $alipayComponent->getNotifyEntity();
			$notify_data = $alipayNotify->decrypt($_POST['notify_data']);
		
			//解析notify_data
			//注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
			$doc = new DOMDocument();
			$doc->loadXML($notify_data);
			
		
			if ( ! empty($doc->getElementsByTagName( "notify" )->item(0)->nodeValue)) {
				//商户订单号
				$orderId = $out_trade_no = $doc->getElementsByTagName( "out_trade_no" )->item(0)->nodeValue;
				//支付宝交易号
				$trade_no = $doc->getElementsByTagName( "trade_no" )->item(0)->nodeValue;
				//交易状态
				$trade_status = $doc->getElementsByTagName( "trade_status" )->item(0)->nodeValue;
		
				if ($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
					//处理订单为支付成功 已处理过订单会返回false
					if ($payComponent->orderPaySuccess($orderId, NULL, FALSE)) {
						//执行订单逻辑程序，完成订单
						$payComponent->completeOrder(array('order_id' => $orderId, 'payid' => 11));
						//记录支付宝支付记录
						$record = $_POST;
						$record['out_trade_no'] = $out_trade_no;
						$record['trade_no'] = $trade_no;
						$record['trade_status'] = $trade_status;
						AlipayTradeLog::recordPay($record);
					}
				}
			}
		}
	}
	
	/**
	 * 支付宝client同步回调地址
	 */
	public function actionClientAlipayReturn()
	{
	    $orderId = Yii::$app->request->get('out_trade_no', '');
	    
	    if ( ! empty($orderId))
	    {
	        $payComponent = $this->module->basePay;
	        //获取支付宝组件
	        $alipayComponent = $this->module->getPayComponent('clientAlipay');
	        
	        //验证支付参数
	        if ($alipayComponent->verifyReturn())
	        {
	            //处理订单为支付成功 已处理过订单会返回false
	            if ($payComponent->orderPaySuccess($orderId, NULL, FALSE))
	            {
	                //执行订单逻辑程序，完成订单
	                $payComponent->completeOrder(array('order_id' => $orderId, 'payid' => 11));
	                //记录支付宝支付记录
	                AlipayTradeLog::recordPay($_REQUEST);
	            }
	        }
	        
	        $payComponent->showReturn(array('order' => $orderId, 'controller' => &$this, 'type' => 'clientAlipay'));
	    }
	}
	
	/**
	 * 支付宝client异步验证
	 */
	public function actionClientAlipayNotify()
	{
	    $payComponent = $this->module->basePay;
	    $alipayComponent = $this->module->getPayComponent('clientAlipay');
	
	    $verifyResult = $alipayComponent->verifyNotify();
	    
	    if ($verifyResult) {//验证成功
            //商户订单号
            $orderId = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];

            if ($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
                //处理订单为支付成功 已处理过订单会返回false
                if ($payComponent->orderPaySuccess($orderId, NULL, FALSE)) {
                    //执行订单逻辑程序，完成订单
                    $payComponent->completeOrder(array('order_id' => $orderId, 'payid' => 21));
                    //记录支付宝支付记录
                    $record = $_POST;
                    $record['out_trade_no'] = $out_trade_no;
                    $record['trade_no'] = $trade_no;
                    $record['trade_status'] = $trade_status;
                    AlipayTradeLog::recordPay($record);
                }
            }
	    }
	}
	
	/* public function actionRead()
	{
	    echo file_get_contents(Yii::getPathOfAlias('webroot') . '/../runtime/test.txt');
	} */
	
	/**
	 * 易宝同步异步回调地址
	 */
	function actionYeepayReturn()
	{
		$orderId = $_REQUEST['r6_Order'];
		
		if ( ! empty($orderId))
		{
			$payComponent = $this->module->basePay;
			$yeepayComponent = $this->module->getPayComponent('yeepay');

			if ($yeepayComponent->verifyReturn())
			{
				$total_fee = $_REQUEST['r3_Amt'];
				if ($payComponent->orderPaySuccess($orderId, $total_fee))
				{
					$payComponent->completeOrder(array('order_id' => $orderId, 'payid' => 2));

					//易宝传入编码为gbk，转码成utf-8
					$params = StringHelper::iconv('GBK', 'UTF-8', $_REQUEST);
					YeepayTradeLog::recordPay($params);
				}
			}
			
			$payComponent->showReturn(array('order' => $orderId, 'controller' => &$this));
		}
	}

	/**
	 * 微信同步异步回调地址
	 */
	function actionWeixinpayReturn(){
		$msg = array();
		$postStr = file_get_contents('php://input');
		$msg = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

		$orderId = $msg['out_trade_no'];

		if ( ! empty($orderId))
		{
			$_REQUEST['rb_BankId'] = 'weixin';//标记微信支付
			$payComponent = $this->module->basePay;
			
			//验证支付参数
			if ($msg['result_code'] == 'SUCCESS')
			{
				//处理订单为支付成功 已处理过订单会返回false
				if ($payComponent->orderPaySuccess($orderId, NULL, FALSE))
				{
					//执行订单逻辑程序，完成订单
					$payComponent->completeOrder(array('order_id' => $orderId, 'payid' => 3));//微信
				}
			}
			
			$payComponent->showReturn(array('order' => $orderId, 'controller' => &$this, 'type' => 'wxPay'));
		}
		
		//file_put_contents('wxpay.txt', var_export($msg, true) );
		/*
		array (
		  'appid' => 'wx79d2d0883bd09bf7',
		  'bank_type' => 'CFT',
		  'cash_fee' => '1',
		  'fee_type' => 'CNY',
		  'is_subscribe' => 'Y',
		  'mch_id' => '10028642',
		  'nonce_str' => 'ekxjhoz4cu8ivbq3199iqb2jbwn720zz',
		  'openid' => 'oKMHmjg3T9uw_mUs1U3vAkO5G7AU',
		  'out_trade_no' => '1601220000025766',
		  'result_code' => 'SUCCESS',
		  'return_code' => 'SUCCESS',
		  'sign' => 'F56E2E4F8D94513CD31E73C8B02A12D7',
		  'time_end' => '20160122150409',
		  'total_fee' => '1',
		  'trade_type' => 'JSAPI',
		  'transaction_id' => '1006070610201601222839037347',
		)
		*/
	}

}