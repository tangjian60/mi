<?php
namespace common\extensions\logging;
use \CLogRoute;

class MongoDbRoute extends CLogRoute
{

    protected $_db = null;
    protected $_collection = null;

    protected function processLogs($logs)
    {
        $this->getDbConnection();

        $data = array();
        foreach ($logs as $log) {
            array_push($data, array(
                'level' => $log[1],
                'category' => $log[2],
                'logtime' => (int)$log[3],
                'message' => $log[0],
            ));
        }


        $this->_collection->batchInsert($data);
    }

    protected function getDbConnection()
    {
        if ($this->_db !== null) {
            return $this->_db;
        } else {
            $params = \Yii::app()->params['setting']['mongodb'];
            if (empty($params)) {
                throw new \Exception('not found mongodb config from params');
            }

            $dsn = $this->getDsn($params);
            $this->_db = new \MongoClient($dsn);

            $auth = false;
            if (isset($params['require_auth']) && $params['require_auth']) {
                $username = $params['username'];
                $password = $params['password'];
                $auth = true;
            }

            $this->_db = $this->_db->$params['database'];
            if ($auth) {
                $message = $this->_db->authenticate($username, $password);

                //auth faile
                if ($message['ok'] == 0) {
                    throw new \Exception("mongodb auth faile. message:{$message['errmsg']}");
                }
            }


            $this->_collection = $this->_db->$params['collection'];
        }
    }

    protected function getDsn($params)
    {
        $dsn = 'mongodb://';
        if (empty($params)) {
            throw new \Exception('param can not empty. please check');
        }

        if (!isset($params['host']) || !$params['host']) {
            throw new \Exception('param can not empty. please check');
        }

        $dsn = "${params['host']}";

        if (isset($params['port'])) {
            $dsn .= $params['port'] ? ':27017' : ':' . $params['port'];
        }

        return $dsn;
    }
}