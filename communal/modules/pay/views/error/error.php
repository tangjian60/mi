<?php 
$host = Yii::app()->request->hostInfo;
if (preg_match('/meadin/', $host))
{
	include 'error_meadin.php';
}
else
{
	include 'error_veryeast.php';
}