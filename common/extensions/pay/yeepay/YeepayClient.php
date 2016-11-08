<?php
namespace common\extensions\pay\yeepay;
use \Yii;
use \HttpClient;
use common\helpers\StringHelper;

class YeepayClient{
	
	//业务类型 p0_Cmd值 普通类型cmd
	const COMMON_CMD	= 'Buy';
	
	//退款p0_Cmd
	const REFUND_CMD	= 'RefundOrd';
	
	//订单查询p0_Cmd
	const QUERY_CMD		= 'QueryOrdDetail';
	
	//返回成功的code
	const RETURN_SUCCESS_CODE	= 1;
	
	//商户编号p1_MerId,以及密钥merchantKey 需要从易宝支付平台获得
	public $merId;
	
	//密钥merchantKey
	public $merchantKey;
	
	//送货地址  为"1": 需要用户将送货地址留在易宝支付系统;为"0": 不需要，默认为 "0".
	public $SAF = '0';
	    
	//交易币种,固定值"CNY".
	public $cur	= 'CNY';
	
	//应答机制  默认为"1": 需要应答机制;
	public $needResponse	= '1';
	
	//通用接口请求地址
	public $yeepayGateway	= 'https://www.yeepay.com/app-merchant-proxy/node';		//接口测试请求地址'http://tech.yeepay.com:8080/robot/debug.action'
	
	//退款接口正式请求地址
	public $yeepayRefundUrl = 'https://www.yeepay.com/app-merchant-proxy/command';
	
	//订单查询接口正式请求地址
	public $yeepayQueryUrl	= 'https://www.yeepay.com/app-merchant-proxy/command';	//接口测试请求地址'http://tech.yeepay.com:8080/robot/debug.action'
	
	//类中全局默认编码，所有数据调用时都需要转成默认编码
	public $defaultCharset	= 'GBK';
	
	//调用易宝接口需要的字符编码，目前就GBK的
	public $yeepayCharset	= 'GBK';
	
	//默认支付完成返回地址
	public $returnUrl;
	
	//是否写日志
	public $isWriteLog		= FALSE;
	
	//日志保存路径
	public $logBasePath;
	
	//日志文件名
	public $logName			= 'YeePay_HTML.log';
	
	protected $_returnParams		= array();
	protected $_refundReturnParams	= array();
	protected $_queryReturnParams	= array();
	
	function __construct($merId = '', $merchantKey = '')
	{
		$this->merId = $merId;
		$this->merchantKey = $merchantKey; 
		Yii::$classMap['HttpClient'] = __DIR__.'/lib/HttpClient.class.php';
	}
	
	/**
	 * init
	 * 传入参数数组可初始化参数值
	 * 
	 * @param	array $params 参数数组
	 * @return	
	 */
	function init(array $params = array())
	{
		if ( ! empty($params))
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
	}
	
	/**
	 * submitForm
	 * 构造易宝支付表单字符串并返回
	 * 
	 * @param	array $params 参数数组
	 * 	array(
	 * 		'order'=>'123456',	//商户订单号,选填.若不为""，提交的订单号必须在自身账户交易中唯一;为""时，易宝支付会自动生成随机的商户订单号.
	 * 		'amt'=>'0.01',		//支付金额,必填.单位:元，精确到分
	 * 		'pid'=>'east测试充值',	//商品名称,用于支付时显示在易宝支付网关左侧的订单产品信息.
	 * 		'pcat'=>'测试',		//商品种类
	 * 		'pdesc'=>'east',	//商品描述
	 * 		'url'=>'http://',	//商户接收支付成功数据的地址,支付成功后易宝支付会向该地址发送两次成功通知.
	 * 		'MP'=>'extend',		//商户扩展信息，商户可以任意填写1K 的字符串,支付成功时将原样返回
	 * 		'frpId'=>'',		//支付通道编码，默认为""，到易宝支付网关.若不需显示易宝支付的页面，直接跳转到各银行、神州行支付、骏网一卡通等支付页面，该字段可依照附录:银行列表设置参数值.
	 * 
	 * 		'cur'=>'CNY',		//交易币种,固定值"CNY".
	 * 		'SAF'=>'0',			//送货地址  为"1": 需要用户将送货地址留在易宝支付系统;为"0": 不需要，默认为 "0".
	 * 		'needResponse'=>'1',//应答机制  默认为"1": 需要应答机制;
	 * 	)
	 * 
	 * @param	string $inCharset 输入字符编码，默认UTF-8
	 * @param	string $outCharset 输出字符编码，默认UTF-8
	 * @return	string
	 */
	function submitForm(array $params, $inCharset = 'UTF-8', $outCharset = 'UTF-8')
	{
		$params = $this->initSubmitParam($params, $inCharset);
		$requestParam = array(
					'p0_Cmd'	=> self::COMMON_CMD,
					'p1_MerId'	=> $this->merId,
					'p2_Order'	=> $params['order'],
					'p3_Amt'	=> $params['amt'],
					'p4_Cur'	=> $params['cur'],
					'p5_Pid'	=> $params['pid'],
					'p6_Pcat'	=> $params['pcat'],
					'p7_Pdesc'	=> $params['pdesc'],
					'p8_Url'	=> $params['url'],
					'p9_SAF'	=> $params['SAF'],
					'pa_MP'		=> $params['MP'],
					'pd_FrpId'	=> $params['frpId'],
					'pr_NeedResponse'	=> $params['needResponse'],
					'hmac'		=> $this->getRequestHmac($params)
				);
		$form = $this->buildRequestForm($requestParam);
		return self::iconv($this->defaultCharset, $outCharset, $form);
	}
	
	/**
	 * verifyReturn
	 * 检测支付返回是否合法，支付是否成功
	 * 
	 * @return	bool
	 */
	function verifyReturn()
	{
		if ($this->initReturnParam())
		{
			if ($this->_returnParams['hmac'] == $this->getReturnHmac($this->_returnParams))
			{
				if ($this->_returnParams['code'] == self::RETURN_SUCCESS_CODE)
				{
					return TRUE;
				}
			}
		}
		return FALSE;
	}
	
	/**
	 * getReturnParam
	 * 获取支付完成后返回订单详情verifyReturn验证成功后调用
	 *
	 * @param	string $outCharset 输出字符编码，默认UTF-8
	 * @return	array
	 */
	function getReturnParam($outCharset = 'UTF-8')
	{
		return self::iconv($this->defaultCharset, $outCharset, $this->_returnParams);
	}
	
	/**
	 * refund
	 * 退款，返回bool类型退款是否成功
	 * 
	 * @param	array $params 传入$params参数数组
	 * array(
	 * 		'trxId'=>'',		//易宝支付交易流水号
	 * 		'amt'=>'0.01',		//支付金额,必填.单位:元，精确到分
	 * 		'desc'=>'测试退款',	//详细描述退款原因的信息.
	 * 
	 * 		'cur'=>'CNY',		//交易币种，默认CNY，选填
	 * 	)
	 * @param	string $inCharset 输入字符编码，默认UTF-8
	 * @return	bool
	 */
	function refund(array $params, $inCharset = 'UTF-8')
	{
		$params = $this->initRefundParam($params, $inCharset);
		$refundParam = array(
					'p0_Cmd'	=> self::REFUND_CMD,
					'p1_MerId'	=> $this->merId,
					'pb_TrxId'	=> $params['trxId'],
					'p3_Amt'	=> $params['amt'],
					'p4_Cur'	=> $params['cur'],
					'p5_Desc'	=> $params['desc'],
					'hmac'		=> $this->getRefundHmac($params)
				);
		$refundParam = self::iconv($this->defaultCharset, $this->yeepayCharset, $refundParam);
		$contents = HttpClient::quickPost($this->yeepayRefundUrl, $refundParam);
		if ($this->initRefundReturnParam($contents))
		{
			if ($this->_refundReturnParams['hmac'] == $this->getRefundReturnHmac($this->_refundReturnParams))
			{
				if ($this->_refundReturnParams['code'] == 1)
				{
					return TRUE;
				}
			}
		}
		return FALSE;
	}
	
	/**
	 * getRefundReturnParam
	 * 获取退款订单详细信息，退款$this->refund()成功后调用
	 * 
	 * @param	string $outCharset 输出字符编码，默认UTF-8
	 * @return	array
	 */
	function getRefundReturnParam($outCharset = 'UTF-8')
	{
		return self::iconv($this->defaultCharset, $outCharset, $this->_refundReturnParams);
	}
	
	/**
	 * queryDetail
	 * 查询订单详情
	 *
	 * @param	string $order 订单号
	 * @param	string $inCharset 输入字符编码，默认UTF-8
	 * @param	string $outCharset 输出字符编码，默认UTF-8
	 * @return	mixed array or null
	 */
	function queryDetail($order, $inCharset = 'UTF-8', $outCharset = 'UTF-8')
	{
		$order = self::iconv($inCharset, $this->defaultCharset, $order);
		$queryParam = array(
					'p0_Cmd'	=> self::QUERY_CMD,
					'p1_MerId'	=> $this->merId,
					'p2_Order'	=> $order,
					'hmac'		=> $this->getQueryHmac(array('order'=>$order))
				);
		$queryParam = self::iconv($this->defaultCharset, $this->yeepayCharset, $queryParam);
		$contents = HttpClient::quickPost($this->yeepayQueryUrl, $queryParam);
		if ($this->initQueryReturnParam($contents))
		{
			if ($this->_queryReturnParams['hmac'] == $this->getQueryReturnHmac($this->_queryReturnParams))
			{
				return self::iconv($this->defaultCharset, $outCharset, $this->_queryReturnParams);
			}
		}
	}
	
	/**
	 * initSubmitParam
	 * 初始化支付提交参数，为未传入参数赋默认值
	 *
	 * @param	string $params 参数数组
	 * @param	string $inCharset 输入字符编码，默认UTF-8
	 * @return	array
	 */
	function initSubmitParam(array &$params, $inCharset = 'UTF-8')
	{
		$default = array(
				'order'	=> '',
				'amt'	=> '',
				'cur'	=> $this->cur,
				'pid'	=> '',
				'pcat'	=> '',
				'pdesc'	=> '',
				'url'	=> $this->returnUrl,
				'SAF'	=> $this->SAF,
				'MP'	=> '',
				'frpId'	=> '',
				'needResponse'	=> $this->needResponse
		);
	
		$params = array_merge($default, $params);
		$params= self::iconv($inCharset, $this->defaultCharset, $params);
		return $params;
	}
	
	/**
	 * initReturnParam
	 * 初始化支付完成后易宝传入的参数，判断并返回参数是否完整
	 *
	 * @return	bool
	 */
	function initReturnParam()
	{
		if (
				isset($_REQUEST['r0_Cmd'])
				&& isset($_REQUEST['r1_Code'])
				&& isset($_REQUEST['r2_TrxId'])
				&& isset($_REQUEST['r3_Amt'])
				&& isset($_REQUEST['r4_Cur'])
				&& isset($_REQUEST['r5_Pid'])
				&& isset($_REQUEST['r6_Order'])
				&& isset($_REQUEST['r7_Uid'])
				&& isset($_REQUEST['r8_MP'])
				&& isset($_REQUEST['r9_BType'])
				&& isset($_REQUEST['hmac'])
		)
		{
			$this->_returnParams['cmd']		= $_REQUEST['r0_Cmd'];
			$this->_returnParams['code']	= $_REQUEST['r1_Code'];
			$this->_returnParams['trxId']	= $_REQUEST['r2_TrxId'];
			$this->_returnParams['amt']		= $_REQUEST['r3_Amt'];
			$this->_returnParams['cur']		= $_REQUEST['r4_Cur'];
			$this->_returnParams['pid']		= $_REQUEST['r5_Pid'];
			$this->_returnParams['order']	= $_REQUEST['r6_Order'];
			$this->_returnParams['uid']		= $_REQUEST['r7_Uid'];
			$this->_returnParams['MP']		= $_REQUEST['r8_MP'];
			$this->_returnParams['bType']	= $_REQUEST['r9_BType'];
			$this->_returnParams['hmac']	= $_REQUEST['hmac'];
			$this->_returnParams = self::iconv($this->yeepayCharset, $this->defaultCharset, $this->_returnParams);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * initRefundParam
	 * 初始化退款申请参数
	 * @param	array $params
	 * @param	string $inCharset 输入字符编码，默认UTF-8
	 * @return	array
	 */
	function initRefundParam(array &$params, $inCharset = 'UTF-8')
	{
		$default = array(
				'trxId'	=> '',
				'amt'	=> '',
				'cur'	=> $this->cur,
				'desc'	=> ''
		);
		$params = array_merge($default, $params);
		$params = self::iconv($inCharset, $this->defaultCharset, $params);
		return $params;
	}
	
	/**
	 * initRefundReturnParam
	 * 初始化退款后易宝传入的参数，解析并且判断返回参数是否完整
	 * @param	string $contents 易宝返回的字符串
	 * @return	bool
	 */
	function initRefundReturnParam($contents = '')
	{
		$this->_refundReturnParams = NULL;
		$params = $this->parseReturn($contents);
		if (
				isset($params['r0_Cmd'])
				&& isset($params['r1_Code'])
				&& isset($params['r2_TrxId'])
				&& isset($params['r3_Amt'])
				&& isset($params['r4_Cur'])
				&& isset($params['hmac'])
		)
		{
			$this->_refundReturnParams['cmd']	= $params['r0_Cmd'];
			$this->_refundReturnParams['code']	= $params['r1_Code'];
			$this->_refundReturnParams['trxId'] = $params['r2_TrxId'];
			$this->_refundReturnParams['amt']	= $params['r3_Amt'];
			$this->_refundReturnParams['cur']	= $params['r4_Cur'];
			$this->_refundReturnParams['hmac']	= $params['hmac'];
			$this->_refundReturnParams = self::iconv($this->yeepayCharset, $this->defaultCharset, $this->_refundReturnParams);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * initQueryReturnParam
	 * 初始化查询后易宝传入的参数，解析并且判断返回参数是否完整
	 * 
	 * @param	string $contents 易宝返回的字符串
	 * @return	bool
	 */
	function initQueryReturnParam($contents = '')
	{
		$this->_queryReturnParams = NULL;
		$params = $this->parseReturn($contents);
		if (
			isset($params['r0_Cmd'])
			&& isset($params['r1_Code'])
			&& isset($params['r2_TrxId'])
			&& isset($params['r3_Amt'])
			&& isset($params['r4_Cur'])
			&& isset($params['r5_Pid'])
			&& isset($params['r6_Order'])
			&& isset($params['r8_MP'])
			&& isset($params['rb_PayStatus'])
			&& isset($params['rc_RefundCount'])
			&& isset($params['rd_RefundAmt'])
			&& isset($params['hmac'])
			)
		{
			$this->_queryReturnParams['cmd']		= $params['r0_Cmd'];		//业务类型 
			$this->_queryReturnParams['code']		= $params['r1_Code'];		//查询结果状态码
			$this->_queryReturnParams['trxId']		= $params['r2_TrxId'];		//易宝支付交易流水号
			$this->_queryReturnParams['amt']		= $params['r3_Amt'];		//支付金额
			$this->_queryReturnParams['cur']		= $params['r4_Cur'];		//交易币种
			$this->_queryReturnParams['pid']		= $params['r5_Pid'];		//商品名称
			$this->_queryReturnParams['order']		= $params['r6_Order'];		//商户订单号
			$this->_queryReturnParams['MP']			= $params['r8_MP'];			//商户扩展信息
			$this->_queryReturnParams['payStatus']	= $params['rb_PayStatus'];	//支付状态
			$this->_queryReturnParams['refundCount']= $params['rc_RefundCount'];//已退款次数
			$this->_queryReturnParams['refundAmt']	= $params['rd_RefundAmt'];	//已退款金额
			$this->_queryReturnParams['hmac']		= $params['hmac'];			//校验码
			$this->_queryReturnParams = self::iconv($this->yeepayCharset, $this->defaultCharset, $this->_queryReturnParams);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * getRequestHmac
	 * 生成支付请求签名串
	 *
	 * @param	array $params
	 * @return	string
	 */
	function getRequestHmac(array $params)
	{
		$signString = '';
		$signString .= self::COMMON_CMD;
		$signString .= $this->merId;
		$signString .= $params['order'];
		$signString .= $params['amt'];
		$signString .= $params['cur'];
		$signString .= $params['pid'];
		$signString .= $params['pcat'];
		$signString .= $params['pdesc'];
		$signString .= $params['url'];
		$signString .= $params['SAF'];
		$signString .= $params['MP'];
		$signString .= $params['frpId'];
		$signString .= $params['needResponse'];
		
		$hmac = $this->hmacMd5($signString, $this->merchantKey);
		$this->writeLog($params['order'], $signString, $hmac);
		return $hmac;
	}
	
	/**
	 * getReturnHmac
	 * 生成支付后返回参数的签名串
	 *
	 * @param	array $params
	 * @return	string
	 */
	function getReturnHmac(array $params)
	{
		$signString = '';					//取得加密前的字符串
		$signString .= $this->merId;		//加入商家ID
		$signString .= $params['cmd'];		//加入消息类型
		$signString .= $params['code'];		//加入业务返回码
		$signString .= $params['trxId'];	//加入交易ID
		$signString .= $params['amt'];		//加入交易金额
		$signString .= $params['cur'];		//加入货币单位
		$signString .= $params['pid'];		//加入产品Id
		$signString .= $params['order'];	//加入订单ID
		$signString .= $params['uid'];		//加入用户ID
		$signString .= $params['MP'];		//加入商家扩展信息
		$signString .= $params['bType'];	//加入交易结果返回类型
		
		$hmac = $this->hmacMd5($signString, $this->merchantKey);
		$this->writeLog($params['order'], $signString, $hmac);
		return $hmac;
	}
	
	/**
	 * getRefundHmac
	 * 生成退款请求的签名串
	 *
	 * @param	array $params
	 * @return	string
	 */
	function getRefundHmac(array $params)
	{
		$signString = '';
		$signString .= self::REFUND_CMD;
		$signString .= $this->merId;
		$signString .= $params['trxId'];
		$signString .= $params['amt'];
		$signString .= $params['cur'];
		$signString .= $params['desc'];
		
		$hmac = $this->hmacMd5($signString, $this->merchantKey);
		$this->writeLog($params['trxId'], $signString, $hmac);
		return $hmac;
	}
	
	/**
	 * getRefundReturnHmac
	 * 生成退款返回参数的签名串
	 *
	 * @param	array $params
	 * @return	string
	 */
	function getRefundReturnHmac(array $params)
	{
		$signString = '';
		$signString .= $params['cmd'];		//加入业务类型
		$signString .= $params['code'];		//加入退款申请是否成功
		$signString .= $params['trxId'];	//加入易宝支付交易流水号
		$signString .= $params['amt'];		//加入退款金额
		$signString .= $params['cur'];		//加入交易币种
		
		$hmac = $this->hmacMd5($signString, $this->merchantKey);
		$this->writeLog($params['trxId'], $signString, $hmac);
		return $hmac;
	}
	
	/**
	 * getQueryHmac
	 * 生成订单查询请求的签名串
	 *
	 * @param	array $params
	 * @return	string
	 */
	function getQueryHmac(array $params)
	{
		$signString = '';
		$signString .= self::QUERY_CMD;
		$signString .= $this->merId;
		$signString .= $params['order'];
		
		$hmac = $this->hmacMd5($signString, $this->merchantKey);
		$this->writeLog($params['order'], $signString, $hmac);
		return $hmac;
	}
	
	/**
	 * getQueryReturnHmac
	 * 生成订单查询返回参数的签名串
	 *
	 * @param	array $params
	 * @return	string
	 */
	function getQueryReturnHmac(array $params)
	{
		$signString = '';
		$signString .= $params['cmd'];
		$signString .= $params['code'];
		$signString .= $params['trxId'];
		$signString .= $params['amt'];
		$signString .= $params['cur'];
		$signString .= $params['pid'];
		$signString .= $params['order'];
		$signString .= $params['MP'];
		$signString .= $params['payStatus'];
		$signString .= $params['refundCount'];
		$signString .= $params['refundAmt'];
		
		$hmac = $this->hmacMd5($signString, $this->merchantKey);
		$this->writeLog($params['order'], $signString, $hmac);
		return $hmac;
	}
	
	/**
	 * hmacMd5
	 * 生成签名串函数
	 *
	 * @param	string $data 原字符串
	 * @param	string $key 密钥key
	 * @return	string
	 */
	function hmacMd5($data, $key)
	{
		// RFC 2104 HMAC implementation for php.
		// Creates an md5 HMAC.
		// Eliminates the need to install mhash to compute a HMAC
		// Hacked by Lance Rushing(NOTE: Hacked means written)
	
		$key	= self::iconv($this->defaultCharset, 'UTF-8', $key);
		$data	= self::iconv($this->defaultCharset, 'UTF-8', $data);
	
		$b = 64; // byte length for md5
		if (strlen($key) > $b)
		{
			$key = pack("H*",md5($key));
		}
		$key = str_pad($key, $b, chr(0x00));
		$ipad = str_pad('', $b, chr(0x36));
		$opad = str_pad('', $b, chr(0x5c));
		$k_ipad = $key ^ $ipad ;
		$k_opad = $key ^ $opad;
	
		return md5($k_opad . pack("H*",md5($k_ipad . $data)));
	}
	
	/**
	 * parseReturn
	 * 解析易宝返回字符串为参数数组
	 *
	 * @param	string $contents
	 * @return	array
	 */
	function parseReturn($contents)
	{
		$result = explode("\n", $contents);
		$params = array();
		foreach ($result as $val)
		{
			if (strpos($val, '=') > 0)
			{
				list($k, $v) = explode("=", $val);
				$params[$k] = urldecode($v);
			}
		}
		return $params;
	}
	
	/**
	 * buildRequestForm
	 * 创建易宝提交表单
	 *
	 * @param	array $params 参数
	 * @param	string $method 表单提交方法，默认post
	 * @return	string
	 */
	function buildRequestForm(array $params, $method = 'post')
	{
		$html = "<form id='yeepay' name='yeepay' action='".$this->yeepayGateway."' method='".$method."'>";
		foreach ($params as $key => $val)
		{
			$html .= '<input type="hidden" name="'.$key.'" value="'.$val.'"/>';
		}
		$html .= "<script>document.forms['yeepay'].submit();</script>";
		return $html;
	}
	
	/**
	 * writeLog
	 * 记录易宝日志
	 *
	 * @param	string $orderId 订单号
	 * @param	string $str 参数拼接串
	 * @param	string $hmac 参数拼接串加密后的字符串
	 * @return	null
	 */
	function writeLog($orderId = '', $str = '', $hmac = '')
	{
		if ($this->isWriteLog && $this->logBasePath !== NULL)
		{
			$logPath = trim($this->logBasePath, '/').'/'.$this->logName;
			$f=fopen($logPath,"a+");
			fwrite($f, "\r\n".date("Y-m-d H:i:s")."|orderid[".$orderId."]|str[".$str."]|hmac[".$hmac."]");
			fclose($f);
		}
	}
	
	/**
	 * iconv
	 * 转换字符编码
	 *
	 * @param	string $inCharset 输入字符编码
	 * @param	string $outCharset 输出字符编码
	 * @param	mixed string or array $data 要转换的数据，字符串或者数组
	 * @return	mixed string or array
	 */
	public static function iconv($inCharset, $outCharset, &$data = '')
	{
		if (strtolower($inCharset) != strtolower($outCharset))
		{
			$data = StringHelper::iconv($inCharset, $outCharset, $data);
		}
		return $data;
	}
}