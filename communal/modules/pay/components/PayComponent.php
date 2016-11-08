<?php

namespace communal\modules\pay\components;

use yii\base\Component;
use communal\models\DfPay;
use communal\models\df_pay\PayOrder;
use communal\models\df_pay\OrderError;

abstract class PayComponent extends Component
{
	
	//支付宝支付pay_id
	const ALIPAY_PAY_ID			= 1;
	
	//易宝支付pay_id
	const YEEPAY_PAY_ID			= 2;
	
	//pay_status未支付
	const PAY_STATUS_NOT_PAID	= 0;
	
	//pay_status已支付
	const PAY_STATUS_HAS_PAID	= 1;
	
	//pay_status已退款
	const PAY_STATUS_REFUND	= 2;

	//成功
	const SUCCESS = 0;

	//event
	const EVENT_CREATE_ORDER = 'createOrder';
	const EVENT_COMPLETE_ORDER  = 'completeOrder';

	public $payTitle	= '';
	
	public $payTips		= '';
	
	/**
	 * 抽象方法完成订单
	 * @param array $params
	 */
	abstract protected function _completeOrder(array $params);
	
	/**
	 * 订单退款
	 * @param array $params
	 */
	protected function _orderRefund(array $params){}
	
	/**
	 * 显示结果
	 */
	public function showReturn(array $params)
	{
		echo '<script type="text/javascript">window.opener=null;window.close();</script>';
		\Yii::app()->end();
	}
	
	/**
	 * 迈粒充值创建订单程序
	 *
	 * @param array $params
	 * @return object or null 订单model
	 */
	public function createOrder(array $params)
	{
		$params['order_title']	= isset($params['order_title']) ? $params['order_title'] : $this->orderTitle;
		
		$orderId = DfPay::callFunction('f_create_order', array($params['userid'], $this->productId, $params['order_title'], $params['pay_amount']));
		$params['order_id'] = $orderId;
		
		$this->triggerCreateOrder($orderId, $params);
		
		if (isset($params['order_detail'])) DfPay::callFunction('f_set_field', array($orderId, 'order', 'order_detail', $params['order_detail']));
		
		return $orderId;
	}   
	
	/**
	 * 完成订单
	 * @param integer $orderId
	 */
	public function completeOrder(array $params)
	{
		$orderId = $params['order_id'];
		try
		{
			$this->_completeOrder($params);
			
			if (isset($params['payid'])) $this->setPayPattern($orderId, $params['payid']);
			$this->recordCompleteOrder($orderId);

			$this->triggerCompleteOrder($orderId);
			
			return TRUE;
		}
		catch(\Exception $e) 
		{
			$this->logError($orderId, $e->getMessage());
		}
	}

	protected function triggerCreateOrder($order_num, $params)
	{
		$this->trigger(self::EVENT_CREATE_ORDER, new OrderEvent(['order_num' => $order_num, 'params' => $params]));
	}

	protected function triggerCompleteOrder($order_num)
    {
        $this->trigger(self::EVENT_COMPLETE_ORDER, new OrderEvent(['order_num' => $order_num]));
    }
	
	/**
	 * 订单支付成功
	 *
	 * @param string $orderId
	 * @param decimal $totalFee //支付金额
	 */
	public function orderPaySuccess($orderId, $totalFee, $checkFee = TRUE)
	{
		//检查订单金额跟支付金额是否一致
		if ( ( ! $checkFee) || $this->checkOrderPay($orderId, $totalFee))
		{
			$flag = DfPay::callFunction('f_set_field', array($orderId, 'order', 'pay_status', self::PAY_STATUS_HAS_PAID));
			if ($flag == self::SUCCESS)
			{
				$this->remarkPaySuccess($orderId);
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * 订单退款
	 */
	public function orderRefund(array $params)
	{
		$orderId = $params['order_id'];
		
		try
		{
			$this->_orderRefund($params);
			DfPay::callFunction('f_set_field', array($orderId, 'order', 'pay_status', self::PAY_STATUS_REFUND));
			$this->recordOrderRefund($orderId);
		}
		catch(\Exception $e)
		{
			$this->logError($orderId, $e->getMessage());
		}
	}
	
	/**
	 * 记录异常
	 */
	public function logError($orderId, $content = '')
	{
		return OrderError::record($orderId, $content);
	}
	
	/**
	 * 获取order模型
	 */
	public function getOrderModel($orderId)
	{
		return PayOrder::findOne(['order_id' => $orderId]);
	}
	
	/**
	 * 设置支付类型
	 */
	public function setPayPattern($orderId, $payid)
	{
		DfPay::callFunction('f_set_field', array($orderId, 'order', 'pay_id', $payid));
	}
	
	/**
	 * 备注支付成功
	 * @param string $orderId
	 */
	public function remarkPaySuccess($orderId)
	{
		$string = date('Y-m-d H:i:s').' 支付成功'."\n";
		DfPay::callFunction('f_set_field', array($orderId, 'order', 'order_remark', $string));
	}
	
	/**
	 * 记录订单完成
	 * @param string $orderId
	 * @param string $userid
	 */
	public function recordCompleteOrder($orderId, $userid = 0)
	{
		$operateDetail = '订单完成';
		DfPay::callFunction('f_set_field', array($orderId, 'order', 'order_remark', date('Y-m-d H:i:s').' '.$operateDetail."\n"));
	}
	
	/**
	 * 记录订单退款
	 */
	public function recordOrderRefund($orderId)
	{
		$operateDetail = '订单退款';
		DfPay::callFunction('f_set_field', array($orderId, 'order', 'order_remark', date('Y-m-d H:i:s').' '.$operateDetail."\n"));
	}
	
	/**
	 * 检查订单金额和支付金额是否一致
	 * 
	 * @param string $orderId
	 * @param decimal $totalFee //支付金额
	 * @return bool
	 */
	protected function checkOrderPay($orderId, $totalFee)
	{
		$orderAmount = DfPay::callFunction('f_get_field', array($orderId, 'order', 'pay_amount'));
		//金额不相等
		if ($orderAmount == $totalFee)
		{
			return TRUE;
		}
		else
		{
			$errorDetail = "支付金额".$totalFee."与实际金额".$orderAmount."不一致";
			//记录订单金额不符合异常
			$this->logError($orderId, $errorDetail, 1);
			
			return FALSE;
		}
	}
	
}