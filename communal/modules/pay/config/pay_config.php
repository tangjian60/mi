<?php

$config = [
	'alipay' => [
		'class'			=> 'communal\extensions\pay\alipay\AlipayClient',
		'partner'		=> '2088002281168567',
		'key'			=> 'adwwc3djl6wqdc9foze3i8s9z0n4peoy',
		'seller_email'	=> '439882790@qq.com',
		'notifyUrl'     => 'alipay-notify',
		'returnUrl'     => 'alipay-return',
		'showUrl'       => 'alipay-return',
	],
	'client_alipay' => [
		'class'			=> 'communal\extensions\pay\alipay\ClientAlipayClient',
		'partner'		=> '2088211362651720',
		'key'			=> '6hbwrngwa6ai6nqyldzcsd01wy1bvf6l',
		'seller_email'	=> 'fc@veryeast.com',
		'notifyUrl'     => 'client-alipay-notify',
		'returnUrl'	    => 'client-alipay-return',
		'showUrl'       => 'client-alipay-return',
		'merchantUrl'   => 'client-alipay-return',
	],
	'wap_alipay' => [
		'class'			=> 'communal\extensions\pay\alipay\WapAlipayClient',
		'partner'		=> '2088211362651720',
		'key'			=> '6hbwrngwa6ai6nqyldzcsd01wy1bvf6l',
		'seller_email'	=> 'fc@veryeast.com',
		'notifyUrl'     => 'wap-alipay-notify',
		'returnUrl'     => 'wap-alipay-return',
		'showUrl'       => 'wap-alipay-return',
		'merchantUrl'   => 'wap-alipay-return',
	],
	'yeepay' => [
		'class'			=> 'communal\extensions\pay\yeepay\YeepayClient',
		'merId'			=> '10000703936',
		'merchantKey'	=> 'o5m46x4thecokaabjwuc4oz2zkt88g282uo4ew5hbszrqeq9r7si5fxebd5v',
		'returnUrl'     => 'yeepay-return',
	],
	'weixin' => [
		'class'			=> 'communal\extensions\pay\weixin\WeixinPay',
		'appid'         => 'wx79d2d0883bd09bf7',
		'mch_id'        => '10028642',
		'key'           => 'FB5CCD2F0C788A42B9AC76858808DEF2',
		'appsecret'     => '8facb71c17feef00c4078cde5f2b38e7',
		'returnUrl'     => 'weixinpay-return',
 	],
];

return $config;