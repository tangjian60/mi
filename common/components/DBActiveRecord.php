<?php
namespace common\components;

use \Yii;
use \CDbConnection;
use \CDbException;
use common\helpers\ConnectionHelper;

class DBActiveRecord extends \yii\db\ActiveRecord{

	public static $_dbConnections = array();
	
	/**
	 * @return 数据配置组
	 */
	public static function dbActiveGroup()
	{
		return get_called_class();
	}

    /**
     * Returns the static model of the specified AR class.
     * The model returned is a static instance of the AR class.
     * It is provided for invoking class-level methods (something similar to static class methods.)
     *
     * <pre>
     * public static function model($className=__CLASS__)
     * {
     *     return parent::model($className);
     * }
     * </pre>
     *
     * @param string $className active record class name.
     * @return DBActiveRecord active record model instance.
     */
    public static function model($className=__CLASS__)
	{
		if ($className === __CLASS__ ) 
		{
			$className = get_called_class();
		}
	
		return parent::model($className);
	}

    /**
     * 重写了 CActiveRecord的获取数据库连接，默认从公共配置里面读取连接配置
     *
     * @throws \CDbException
     * @return CDbConnection the database connection used by active record.
     */
	function getDbConnection()
	{
		$activeGroup = $this->dbActiveGroup();
		if(isset(self::$_dbConnections[$activeGroup]))
		{
			return self::$_dbConnections[$activeGroup];
		}
		else
		{
			$dbConnection = ConnectionHelper::get($activeGroup);
			if( ! empty($dbConnection) && $dbConnection instanceof CDbConnection)
			{
				self::$_dbConnections[$activeGroup] = $dbConnection;
				return $dbConnection;
			}
			else
			{
				throw new Exception(Yii::t('yii','Active Record requires a "'.$activeGroup.'" CDbConnection application component.'));
			}
		}
	}
	
	/**
	 * 获取数据库连接，静态方法
	 *
	 * @return \
	 */
	public static function getDb()
	{
		$className = get_called_class();
		return ConnectionHelper::get($className::dbActiveGroup());
	}

    /**
     * 执行数据库function
     *
     * @param $mysqlFunction
     * @param array $params
     * @return 执行结果
     */
	public static function callFunction($mysqlFunction, array $params=array())
	{
		$className = get_called_class();
		$connection = $className::getDb();
		switch ($connection->driverName)
		{
			case 'mysql':
				return ConnectionHelper::callMysqlFunction($connection, $mysqlFunction, $params);
				break;
			default:
				break;
		}
		return FALSE;
	}
	
	/**
	 * 执行存储过程
	 *
	 * @return 执行结果
	 */
	public static function callProcedure($mysqlProcedure, array $params=array())
	{
		$className = get_called_class();
		$connection = $className::getDb();
		switch ($connection->driverName)
		{
			case 'mysql':
				return ConnectionHelper::callMysqlProcedure($className::getDb(), $mysqlProcedure, $params);
				break;
			default:
				break;
		}
		return FALSE;
	}
	
	/**
	 * 获取字段rawName加表别名前缀,主要联表时候防止where条件中字段冲突用的
	 * @param string $columnName
	 * @return string
	 */
	public function getColumnRawName($columnName)
	{
		$prefix = $this->getTableAlias(true) . '.';
		$columns = $this->tableSchema->columns;
		if (isset($columns[$columnName]))
		{
			return $prefix.$columns[$columnName]->rawName;
		}
		else
		{
			return $columnName;
		}
	}
	
	/**
	 * 
	 * @param mixed $criteria
	 */
	public function queryAll($criteria = NULL)
	{
		if ( ! empty($criteria))
		{
			$this->getDbCriteria()->mergeWith($criteria);
		}
		
		$result = $this->getCommandBuilder()
			->createFindCommand($this->tableSchema, $this->getDbCriteria())
			->queryAll();
		
		$this->setDbCriteria(NULL);
		
		return $result;
	}
	
	public function queryRow($criteria = NULL)
	{
		if ($criteria != NULL)
		{
			$this->getDbCriteria()->mergeWith($criteria);
		}
		
		$result = $this->getCommandBuilder()
			->createFindCommand($this->tableSchema, $this->getDbCriteria())
			->queryRow();
		
		$this->setDbCriteria(NULL);
		
		return $result;
	}
	
	public function compare($column, $value, $partialMatch = FALSE, $operator = 'AND')
	{
		$criteria = new \CDbCriteria;
		$column = $this->getColumnRawName($column);
		
		if ($value === array())
		{
			$criteria->condition = "1 = 0";
		}
		else if ($value === '')
		{
			$criteria->condition = $column." = ''";
		}
		else
		{
			$criteria->compare($column, $value, $partialMatch, $operator, TRUE);
		}
		
		$this->getDbCriteria()->mergeWith($criteria);
		
		return $this;
	}
	
	
}