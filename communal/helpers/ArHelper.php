<?php
namespace communal\helpers;

use Yii;
use yii\db\Exception;
use yii\db\Query;
use yii\db\ActiveRecord;

/**
 * Class ArHelper
 * @autor xing wang
 * @package communal\helpers
 */
class ArHelper
{
	/**
	 * get field value
	 * @param string  $string  e.g ve_person.person.true_name_cn
	 * @param mixed $condition
	 */
	public static function field( $string, $condition )
	{
		$params = array_reverse( explode('.', $string) );
		list($field, $table, $db_group) = array_pad($params, 3, null);

		if( $class = self::className( "{$db_group}.{$table}" ) ){
			$result = $class::findOne($condition);
			return $result ? $result->$field : null;
		}else{
			$db = ConnectionHelper::get( $db_group );
			//$condition as primary key
			if( !is_array($condition) && !preg_match('/=/i', $condition ) ){
				$primaryKey = reset( $db->getSchema()->getTableSchema($table)->primaryKey );
				$condition = "$primaryKey=$condition";
			}

			return (new Query())
				->select($field)
				->from($table)
				->where($condition)
				->scalar($db);
		}

		return false;
	}

	/**
	 * set field value
	 * @param string  $string  e.g ve_person.person.true_name_cn
	 * @param mixed $condition
	 * @param string the value of field to set
	 */
	public static function setField($string, $condition, $value){

		$params = array_reverse( explode('.', $string) );
		list($field, $table, $db_group) = array_pad($params, 3, null);

		if( $class = self::className( "{$db_group}.{$table}" ) ){
			$result = $class::findOne($condition);

			if($result){
				$result->$field = $value;
				if(  $result->save())
					return true;
			}
			return false;

		}else{

			$db = ConnectionHelper::get( $db_group );
			//$condition as primary key
			if( !is_array($condition) && !preg_match('/=/i', $condition ) ){
				$primaryKey = reset( $db->getSchema()->getTableSchema($table)->primaryKey );
				$condition = "$primaryKey=$condition";
			}

			return $db->createCommand()->update($table, [$field => $value], $condition)->execute();
		}

	}

	/**
	 * if model exists, return the model class name of $table
	 * @param string  $string  e.g ve_person.person
	 */
	public static function className( $string )
	{
		$params = array_reverse( explode('.', $string) );
		list($table, $db_group) = array_pad($params, 2, null);
		$modelName = self::formatName($table);
		$modelNamespace = '\communal\models';
		if($db_group){

			if( file_exists( self::modelDir() . DIRECTORY_SEPARATOR . $db_group . DIRECTORY_SEPARATOR . "{$modelName}.php")  ){
				return sprintf('%s\%s\%s', $modelNamespace, $db_group, $modelName);
			}

		}else{

			$dirList = self::dirList();
			foreach($dirList as $v){
				if( file_exists($v . DIRECTORY_SEPARATOR . "{$modelName}.php") ){
					$arr = explode('/', $v);
					return sprintf('%s\%s\%s', $modelNamespace, end($arr), $modelName);
				}
			}

		}

		if($db_group){
			return self::generateModel( $string );
		}

		return false;
	}

	/**
	 * generate a model by giving $string
	 * @param string  $string  e.g ve_person.person
	 */
	public static function generateModel( $string )
	{
		$params = array_reverse( explode('.', $string) );
		if(count($params) !== 2) return false;

		list($table, $db_group) = $params;
		$modelName = self::formatName($table);
		$nameSpace = sprintf('communal\models\%s', $db_group );
		$parentClass = sprintf('\communal\models\%s', self::formatName($db_group) );

		$model = <<<EOF
namespace {$nameSpace};
class {$modelName} extends {$parentClass}
{
	public static function tableName()
	{
		return "{$table}";
	}
}
EOF;
		eval($model);
		$class  = sprintf('\%s\%s', $nameSpace, $modelName );
		return $class;
	}

	/**
	 * table name to Model name: person_service => PersonService
	 * @param string  $string  e.g  person_service
	 */
	protected static function formatName( $table )
	{
		return strtr( ucwords( strtr($table, ['_' => ' ']) ) , [' ' => '']);
	}

	/**
	 * list directory under models directory
	 */
	protected static function dirList()
	{
		$dir = self::modelDir();
		$dir .= substr($dir, -1) == '/' ? '' : '/';
		$dirInfo = [];
		foreach ( glob($dir.'*') as $v){
			if(is_dir($v)){
				$dirInfo[] = $v;
			}
		}
		return $dirInfo;
	}

	/**
	 * models directory
	 */
	protected static function modelDir()
	{
		return dirname( __DIR__ ) . '/models';
	}

}