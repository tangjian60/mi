<?php
namespace common\extensions\logging;

use \Yii;
use \CLogRoute;
use common\models\lms_v2\ApplicationLog;


class DBLogRoute extends CLogRoute
{
    public $connectionID;

    public $logTableName = 'YiiLog';

    public $autoCreateLogTable = true;
    /**
     * @var CDbConnection the DB connection instance
     */
    private $_db;

    /**
     * Initializes the route.
     * This method is invoked after the route is created by the route manager.
     */
    public function init()
    {
        parent::init();

        if ($this->autoCreateLogTable) {
            $db = $this->getDbConnection();
            try {
                $db->createCommand()->delete($this->logTableName, '0=1');
            } catch (Exception $e) {
                $this->createLogTable($db, $this->logTableName);
            }
        }
    }

    /**
     * Creates the DB table for storing log messages.
     * @param CDbConnection $db the database connection
     * @param string $tableName the name of the table to be created
     */
    protected function createLogTable($db, $tableName)
    {
        $db->createCommand()->createTable($tableName, array(
            'id' => 'pk',
            'level' => 'varchar(128)',
            'category' => 'varchar(128)',
            'logtime' => 'integer',
            'message' => 'text',
        ));
    }

    /**
     * @return CDbConnection the DB connection instance
     * @throws CException if {@link connectionID} does not point to a valid application component.
     */
    protected function getDbConnection()
    {
        if ($this->_db !== null)
            return $this->_db;
        elseif ($this->_db = ApplicationLog::model()->dbConnection) {
            if (($this->_db = ApplicationLog::model()->dbConnection))
                return $this->_db;
            else
                throw new CException(Yii::t('yii', 'CDbLogRoute.connectionID  does not point to a valid CDbConnection application component.'));
        } else {
            $dbFile = Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'log-' . Yii::getVersion() . '.db';
            return $this->_db = new CDbConnection('sqlite:' . $dbFile);
        }
    }

    /**
     * Stores log messages into database.
     * @param array $logs list of log messages
     */
    protected function processLogs($logs)
    {
        $command = $this->getDbConnection()->createCommand();
        foreach ($logs as $log) {
            $command->insert($this->logTableName, array(
                'level' => $log[1],
                'category' => $log[2],
                'logtime' => (int)$log[3],
                'message' => $log[0],
            ));
        }
    }
}
