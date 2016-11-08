<?php
/**
 * api常量大全
 */

namespace communal\components;


class Constant
{
    /**
     * 美元转人民币的汇率
     */
    const EXCHANGE_RATE_USD = 6.14529946;

    /**
     * 英镑转欧元的汇率
     */
    const EXCHANGE_RATE_GBP = 9.46929194;

    /**
     * 欧元转人民币的汇率
     */
    const EXCHAGE_RATE_EUR = 8.03313545;

    /**
     *  用户头像前缀
     */
    const USER_AVATAR_PREFIX = 'http://f3.v.veimg.cn/veryeast/user_data/';

    /**
     *  企业头像前缀
     */
    const COMPANY_AVATAR_PREFIX = 'http://f3.v.veimg.cn/company_picture/';

    /**
     * 个人所拥有的最大收藏数(废弃)
     */
    const MAX_FAVOURITE_COUT = 500;

    /**
     * 企业前台展示页host
     */
    const COMPANY_HOST = 'http://job.veryeast.cn';

    /**
     * 找工作的域名
     */
    const JOB_SEARCH_HOST = 'http://search.veryeast.cn';

    /**
     * 薪酬范围 [人民币 月薪]
     */
    public static function getSalaryRange()
    {
        return array(
            1  => array(0, 1000),
            2  => array(1001, 2000),
            3  => array(2001, 3000),
            4  => array(3001, 4000),
            5  => array(4001, 5000),
            6  => array(5001, 6000),
            7  => array(6001, 8000),
            8  => array(8001, 10000),
            9  => array(10001, 15000),
            10 => array(15001, 20000),
            11 => array(20001, 30000),
            12 => array(30001, 50000),
            13 => array(50001, 80000),
            14 => array(80001, 100000),
            15 => array(100001, null)
        );
    }
}