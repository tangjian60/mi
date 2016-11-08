<?php
namespace common\extensions\logging;
use \CComponent;
use \ILogFilter;
use \CLogger;
use \Yii;

class LogFilter extends CComponent implements ILogFilter
{
    /**
     * @var boolean whether to prefix each log message with the current user session ID.
     * Defaults to false.
     */
    public $prefixSession = false;
    /**
     * @var boolean whether to prefix each log message with the current user
     * {@link CWebUser::name name} and {@link CWebUser::id ID}. Defaults to false.
     */
    public $prefixUser = false;
    /**
     * @var boolean whether to log the current user name and ID. Defaults to true.
     */
    public $logUser = true;
    /**
     * @var array list of the PHP predefined variables that should be logged.
     * Note that a variable must be accessible via $GLOBALS. Otherwise it won't be logged.
     */
    public $logVars = array('_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_SERVER');


    /**
     * Filters the given log messages.
     * This is the main method of CLogFilter. It processes the log messages
     * by adding context information, etc.
     * @param array $logs the log messages
     * @return array
     */
    public function filter(&$logs)
    {
        if (!empty($logs)) {
            if (($message = $this->getContext()) !== '') {
               foreach($logs as &$row){
                   $row[0] = $row[0] . json_encode($message);
               }
            }
            $this->format($logs);
        }

        return $logs;
    }

    /**
     * Formats the log messages.
     * The default implementation will prefix each message with session ID
     * if {@link prefixSession} is set true. It may also prefix each message
     * with the current user's name and ID if {@link prefixUser} is true.
     * @param array $logs the log messages
     */
    protected function format(&$logs)
    {
        $prefix = '';
        if ($this->prefixSession && ($id = session_id()) !== '')
            $prefix .= "[$id]";
        if ($this->prefixUser && ($user = Yii::app()->getComponent('user', false)) !== null)
            $prefix .= '[' . $user->getName() . '][' . $user->getId() . ']';
        if ($prefix !== '') {
            foreach ($logs as &$log)
                $log[0] = $prefix . ' ' . $log[0];
        }
    }

    /**
     * Generates the context information to be logged.
     * The default implementation will dump user information, system variables, etc.
     * @return string the context information. If an empty string, it means no context information.
     */
    protected function getContext()
    {
        $context = array();
        if ($this->logUser && ($user = Yii::app()->getComponent('user', false)) !== null){
            $context['user'] = 'User: ' . $user->getName() . ' (ID: ' . $user->getId() . ')';
        }

        $context['uri'] = $GLOBALS['_SERVER']['REQUEST_URI'];

        return  $context;
    }
}