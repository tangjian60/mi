<?php

namespace communal\modules\pay;

use Yii;
use yii\base\Module;
use yii\base\Exception;
use communal\helpers\ConfigHelper;

/**
 * configuration like below:
 * 'pay' => [
 *      'class' => 'communal\modules\pay\Pay',
 *      'components' => [
 *      'pay' => [
 *            'class' => 've_my\components\Pay',
 *         ],
 *      ],
 *  ],
 *
 */
class Pay extends Module
{
	public $controllerNamespace = 'communal\modules\controllers';

	public $defaultRoute = 'request';
	
	protected $_components	= [];
	
	public $components = [];
	
	/**
	 * Initializes the gii module.
	 */
	public function init()
	{
		parent::init();
		$this->controllerMap = $this->findGenerators();
	}

	public function getConfig($id)
	{
		$config = Yii::getConfig($id, '@communal/modules/pay/config/pay_config.php');
		array_walk($config, function (&$v, $k){
			if(substr(strtolower($k), -3) === 'url')
				$v = \yii\helpers\Url::to(['/' .$this->id . '/respond/' . $v], true);
		});

		return $config;
	}

	/**
	 * 	创建支付组件
	 */
	public function createPayComponent($id)
	{
		$config = $this->getConfig($id);
		$component = Yii::createObject($config);
		$component->init();
		
		return $component;
	}

	/**
	 * 获取支付基础组件
	 */
	public function getBasePay(){
		if (! isset($this->_components['pay'])){
			if(! isset($this->components['pay']))
				throw new Exception('pay components is not exists');

			$config = $this->components['pay'];
			$this->_components['pay'] = Yii::createObject($this->components['pay']);
		}

		return $this->_components['pay'];
	}

	/**
	 * 获取支付组件
	 */
	public function getPayComponent($id)
	{
		if ( ! isset($this->_components[$id]) )
			$this->_components[$id] = $this->createPayComponent($id);

		return $this->_components[$id];
	}
	
	
	/**
	 * Finds all available code generators and their code templates.
	 * @return array
	 */
	protected function findGenerators()
	{
		return array(
			'request'	=> array(
				'class'	=> '\communal\modules\pay\controllers\RequestController'
			),
			'respond'	=> array(
				'class'	=> '\communal\modules\pay\controllers\RespondController'
			),
		);
	}
	
}