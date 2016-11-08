<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/31
 * Time: 11:53
 */

namespace communal\components\resource;


use Pheanstalk\Exception\ConnectionException;
use Pheanstalk\Pheanstalk;
use Yii;
use yii\base\Exception;

class Task
{
    private static $_instance = NULL;
    private static function getServer()
    {
        $config = Yii::getConfig('Beanstalk');
        $config = explode(':', $config['default']);

        if(self::$_instance === NULL)
            self::$_instance = new Pheanstalk($config[0], $config[1]);
        return self::$_instance;
    }

    /**
     * 添加任务
     *
     * <p>
     *    请先去Coolie里面注册工人以及行为
     *    Task会自动在生产资料里面添加key为time的值 表示添加此任务的时间
     * </p>
     *
     * @param string  $command    工人以及行为 请用.号分开
     * @param array   $production 生产资料
     * @param integer $priority   优先级 默认 1024 ?????
     * @param integer $delay      延迟多少秒去执行此任务 默认为 0
     * @param integer $ttr        任务执行时间 默认为2分钟
     * @param string  $tube       任务所在的管道 默认为 'task'
     * @return integer/FALSE
     */
    public static function add($command, array $production = array(), $priority = 1024, $delay = 0, $ttr = 120, $tube = 'task')
    {

        try {

            $server = self::getServer();

            $production = array_merge($production, array('time' => microtime(1)) );

            $data  = json_encode(array(
                'command'    => $command,
                'production' => $production
            ));

            return $server->useTube($tube)->put($data, $priority, $delay, $ttr);

            /**
             * 队列服务器挂了 得有个解决方法
             */
        } catch (ConnectionException $e) {
            if(YII_DEBUG){
                print $e->getMessage();
            }

            return FALSE;
        }

    }
}