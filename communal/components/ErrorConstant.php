<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/30
 * Time: 13:53
 */

namespace communal\components;


class ErrorConstant
{
    const SSO = 8000;


    const VEP = 10000;

    //投递错误信息
    const VEP_CONCACT_INFO_NOT_COMPLETE = 11001;
    const VEP_RESUME_NOT_MATCH          = 11002;

    const VEP_REACHED_MAX_APPLY_COUNT_PER_COMPANY    = 11011;
    const VEP_REACHED_MAX_APPLY_COUNT_PER_MONTH      = 11012;
    const VEP_TRIGGER_SINGLE_APPLY_DURING_THREE_DAYS = 11013;
    const VEP_REACHED_MAX_APPLY_COUNT_PER_DAY        = 11014;
    const VEP_TRIGGER_SINGLE_APPLY_DURING_ONE_MONTH  = 11015;

    const VEC = 20000;

    //职位错误信息
    const VEC_JOB_NOT_EXISTS  = 21001;
    const VEC_JOB_NOT_VALID   = 21002;
    const VEC_JOB_EXPIRED     = 21003;
    const VEC_JOB_IS_EQUEST   = 21004;

    const VEC_NOT_VIP	= 20001;
    const VEC_JOB_NUM_NOT_ENOUGH	= 21010;
}