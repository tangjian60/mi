<?php
namespace common\extensions\smarty;
use \Yii;
Yii::$classMap['Smarty'] = __DIR__ . '/libs/Smarty.class.php';
Yii::$classMap['Smarty_Internal_Data'] = __DIR__ . '/libs/sysplugins/smarty_internal_data.php';

class CSmarty extends \Smarty{
    
    public $left_delimiter  = '<{';
    
    public $right_delimiter = '}>';
    
    public $use_space_filter  = FALSE;
    
    public function __construct()
    {
        parent::__construct();
        //默认配置
        Yii::registerAutoloader('smartyAutoload');
        $this->compile_dir = Yii::app()->getRuntimePath() . '/smarty/compile';
        $this->cache_dir   = Yii::app()->getRuntimePath() . '/smarty/cache';
        if(method_exists(Yii::app(), 'getViewPath'))
            $this->setTemplateDir(Yii::app()->getViewPath());
    }
    
    public function init()
    {
        //widget
        $this->registerPlugin('function', 'widget', array($this, '_smartyWidget'));
        $this->registerPlugin('function', 'beginWidget', array($this, '_smartyBeginWidget'));
        $this->registerPlugin('function', 'endWidget', array($this, '_smartyEndWidget'));



        // 注册过滤器
        if($this->use_space_filter) {
            $this->registerFilter('pre', array(__CLASS__, 'preFilter'));
            $this->registerFilter('output', array(__CLASS__, 'outputFilter'));
        }
        


        //function
        // $this->registerPlugin('function', 'createUrl', array($this, '_createUrl'));
        // $this->registerPlugin('function', 'createAbsoluteUrl', array($this, '_createAbsoluteUrl'));
    }

    public static function preFilter($tplSource, \Smarty_Internal_Template $template) 
    {
        $tplSource = preg_replace('/ +/', ' ', $tplSource);
        return str_replace(array("\r", "\t"), '', $tplSource);
    }

    public static function outputFilter($tplSource, \Smarty_Internal_Template $template)
    {
        return str_replace("\n", '', $tplSource);
    }
    
    public function renderPartial($file, array $data = array(), $isReturn = FALSE)
    {
        foreach ($data as $key => $value) $this->assign($key, $value);

        $content = $this->fetch($file);
        if ($isReturn) return $content;
        else echo $content;
    }
    
    /**
     * widget标签调用的注册的smarty方法
     * 
     * @param array $params
     * @param object $smarty
     */
    public function _smartyWidget($params, $smarty)
    {
        $return            = isset($params['return']) ? $params['return'] : NULL;
        $className        = isset($params['className']) ? $params['className'] : NULL;
        $properties        = isset($params['properties']) ? $params['properties'] : array();
        $captureOutput    = $return ? TRUE : (isset($params['captureOutput']) ? $params['captureOutput'] : FALSE);
    
        $widget = Yii::app()->controller->widget($className, $properties, $captureOutput);
        if ($return)
        {
            $smarty->assign($return, $widget);
        }
        return '';
    }
    
    /**
     * beginWidget标签调用的注册的smarty方法
     *
     * @param array $params
     * @param object $smarty
     */
    public function _smartyBeginWidget($params, $smarty)
    {
        $return            = isset($params['return']) ? $params['return'] : NULL;
        $className        = isset($params['className']) ? $params['className'] : NULL;
        $properties        = isset($params['properties']) ? $params['properties'] : array();
        
        $widget = Yii::app()->controller->beginWidget($className, $properties);
        if ($return)
        {
            $smarty->assign($return, $widget);
        }
        return '';
    }
    
    /**
     * endWidget标签调用的注册的smarty方法
     *
     * @param array $params
     * @param object $smarty
     */
    public function _smartyEndWidget($params, $smarty)
    {
        $id = isset($params['id']) ? $params['id'] : '';
        Yii::app()->controller->endWidget($id);
        return '';
    }

    /**
     * Smarty 创建URL
     * @see \CApplication::createUrl()
     * @return string
     */
    // public static function _createUrl($params, $smarty)
    // {

    //     $route     = $params['route'];
    //     $args      = isset($params['params']) ? $params['params'] : array();
    //     $ampersand = isset($params['ampersand']) ? $params['ampersand'] : '&';

    //     return \Yii::app()->createUrl($route, $args, $ampersand);
    // }

    /**
     * Smarty 创建绝对URL
     * @see \CApplication::createAbsoluteUrl()
     * @return string
     */
    // public static function _createAbsoluteUrl($params, $smarty)
    // {

    //     $route     = $params['route'];
    //     $args      = isset($params['params']) ? $params['params'] : array();
    //     $ampersand = isset($params['ampersand']) ? $params['ampersand'] : '&';
    //     $schema    = isset($params['schema']) ? $params['schema'] : '&';

    //     return \Yii::app()->createUrl($route, $args, $schema, $ampersand);
    // }
    
}