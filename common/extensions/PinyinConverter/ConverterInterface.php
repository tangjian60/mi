<?php

namespace common\extensions\PinyinConverter;

interface ConverterInterface
{

    /**
     * 初始化操作器
     * 
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function init(array $params = array());

    public function getPinyin($str);

    public function getInitial($str);    
}