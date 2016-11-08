<?php

namespace common\extensions\phppdo;
/**
 * Project:		Yii::PHPPDO
 * File:		CPdoDbConnection.php
 *
 * This extension allow use PHPPDO library for emulate PDO functionality.
 * For use it add in config/main.php:
 *    ...
 *    'db'=>array(
 *       'class'=>'application.extensions.PHPPDO.CPdoDbConnection',
 *	 'pdoClass'=>'PHPPDO',
 *       ...
 *       ),
 *    ...
 *
 * @link http://oleg.in-da.ru/
 * @author Oleg Blednov
 * @version 1.2-r5
 */
use yii\db\Connection;

class CPdoDbConnection extends Connection
{

	protected function createPdoInstance()
	{
		if (!$this->pdoClass) {
			if (extension_loaded('pdo')) $this->pdoClass = 'PDO';
			else $this->pdoClass = 'PHPPDO';
		}

		if ($this->pdoClass == 'PHPPDO' ) {
			if (!class_exists('PDO', false)) {
				require_once('pdoabstract.php');
				require_once('pdostatementabstract.php');
				require_once('pdoexception.php');
			}
			require_once('phppdo.php');
		}

		if(($pos=strpos($this->dsn,':'))!==false)
		{
			$driver=strtolower(substr($this->dsn,0,$pos));

			if ($this->pdoClass == 'PHPPDO')
			{
				require_once('drivers/base_statement.php');
				require_once('drivers/'.$driver.'_statement.php');
			}

		}

		return new $this->pdoClass($this->dsn, $this->username, $this->password, $this->attributes);
	}
}

?>
