<?php
namespace communal\components\helper;


class ArrayHelper
{
    static function toHashMap($arr, $keyField, $valueField = NULL)
    {
        $ret = array();
        if ($valueField)
        {
            foreach ($arr as $row)
            {
                $ret[$row[$keyField]] = $row[$valueField];
            }
        }
        else
        {
            foreach ($arr as $row)
            {
                $ret[$row[$keyField]] = $row;
            }
        }
        return $ret;
    }
}