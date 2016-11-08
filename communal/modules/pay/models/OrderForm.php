<?php

namespace communal\modules\pay\models;

class OrderForm extends \yii\base\Model
{
	
	public $order_id;
	
	public $pay_bank;
	
	public $payid;
	
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return [
			[['order_id', 'pay_bank'], 'required'],
			['pay_bank', 'initPayId'],
		];
	}
	
	public function initPayId($attribute, $params)
	{
		$map = array(
			'alipay' 	=> 1,
			'yeepay'	=> 2,
			'weixin'    => 3,
			'wap_alipay'=> 11,
		    'client_alipay' => 21
		);
		$this->payid = isset($map[$this->pay_bank]) ? $map[$this->pay_bank] : 2;
		//$this->pay_bank = isset($map[$this->pay_bank]) ? 'yeepay' : $this->pay_bank;
	}
	
}

