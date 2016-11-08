<?php
namespace communal\helpers;

use \Yii;
use yii\db\Exception;

class ConnectionHelper{
	
	public static $_connectionComponentPrefix = 'db_';
	public static $_dbConnections		= array();
 	public static $_DBConfigDirAlias	= '@communal/config/database';

    /**
     * 根据数据连接组获取数据库连接
     *
     * @param string $activeGroup 数据库连接组
     * @throws yii\db\Exception
     * @return yii\db\Connection
     */
	public static function get($activeGroup='')
	{
		$activeGroup = trim($activeGroup);
		
		if (empty($activeGroup)){
			return Yii::$app->get('db');
		}

		$id = self::$_connectionComponentPrefix . $activeGroup;
		
		if (isset(self::$_dbConnections[$id])){
			return self::$_dbConnections[$id];
		}

		$component = Yii::$app->get($id, FALSE);

		if (empty($component)){
			$dbDirAlias = self::$_DBConfigDirAlias;
			$config = array();

			if (defined('YII_ENV') && YII_ENV != 'prod')
			{
				try {

					$dbDirAlias .= '/' . YII_ENV;
					$config = require(Yii::getAlias($dbDirAlias) . '.php');
					$config = $config[$activeGroup];

				} catch(\Exception $e) {}
			}

			if (empty($config))
			{
				$config = require(Yii::getAlias($dbDirAlias . '/' .$activeGroup) . '.php');
			}

			$config['class'] = isset($config['class']) ? $config['class'] : '\yii\db\Connection';
			if ( ! isset($config['enabled']) || $config['enabled'])
			{
				unset($config['enabled']);
				Yii::$app->set($id, $config);
				$component = Yii::$app->get($id);

				self::$_dbConnections[$id] = $component;
			}
		}

		if ( ! empty($component) && $component instanceof \yii\db\Connection)
		{
			return $component;
		}
		else
		{
			throw new Exception(Yii::t('yii','\yii\db\Exception '.$activeGroup.' failed to connect.'));
		}
	}
	
	/**
	 * 执行mysql方法
	 * 
	 * @param object $connection 数据库连接对象
	 * @param string $mysqlFunction mysql函数名
	 * @param array  $params 参数数组
	 * 
	 * @return 执行结果
	 */
	public static function callMysqlFunction($connection, $mysqlFunction, array $params=array())
	{
		$paramStr = '';
		if ( ! empty($params))
		{
			$params = array_values($params);
			$paramStr = ':param'.implode(',:param', array_keys($params));
		}
		$sql = 'SELECT '.$mysqlFunction.'('.$paramStr.') AS result;';

		$query = new \yii\db\Query();
		$command = $connection->createCommand($sql);
		if ( ! empty($params))
		{
			foreach ($params as $key=>&$val)
			{
				$command->bindParam(":param".$key,$val);
			}
		}
		$row = $command->queryOne();
		return $row['result'];
	}
	
	/**
	 * 执行mysql存储过程
	 * 
	 * @param object $connection 数据库连接对象
	 * @param string $mysqlProcedure mysql存储过程名
	 * @param array  $params 参数数组
	 * 
	 * @return 执行结果
	 */
	public static function callMysqlProcedure($connection, $mysqlProcedure, array $params=array())
	{
		$paramStr = '';
		if ( ! empty($params))
		{
			$params = array_values($params);
			$paramStr = ':param'.implode(',:param', array_keys($params));
		}
		$sql = 'CALL '.$mysqlProcedure.'('.$paramStr.');';
		$command = $connection->createCommand($sql);
		if ( ! empty($params))
		{
			foreach ($params as $key=>&$val)
			{
				$command->bindParam(":param".$key,$val);
			}
		}
		return $command->queryAll();
	}
	
}