<?php
namespace common\extensions\smarty;
use \CApplicationComponent;
use \IViewRenderer;
use \Yii;

class SmartyViewRenderer extends CApplicationComponent implements IViewRenderer{
	
	private $_smarty;
	
	public $fileExtension	= '.html';
	
	public $smartyComponent	= array();
	
	public static $smartyDefaultClass = 'common\extensions\smarty\CSmarty';
	
	public function __construct()
	{
	}
	
	public function init()
	{
		parent::init();
		$this->smartyComponent['class']	= isset($this->smartyComponent['class']) ? $this->smartyComponent['class'] : self::$smartyDefaultClass;
		$this->_smarty = Yii::createComponent($this->smartyComponent);
	}
	
	public function renderFile($context, $file, $data, $return)
	{
		foreach ($data as $key => $value)
		{
			$this->_smarty->assign($key, $value);
		}
		$return = $this->_smarty->fetch($file);
		if ($return)
		{
			return $return;
		}
		else
		{
			echo $return;
		}
	}

	public function getSmarty()
	{
		return $this->_smarty;
	}
	
}