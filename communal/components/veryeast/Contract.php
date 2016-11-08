<?php
/**
 * 合同相关
 */

namespace communal\components\veryeast;


use communal\models\mssql_jdrc\ContractDetail;

class Contract
{
    /**
     * 判断是否是vip正式会员、包含子公司的情况
     *
     * @param $userid
     * @return bool
     */
    public static function checkIsVip($userid)
    {
        $result = self::isVip($userid);

        //如果不是vip且为子公司时，判断总公司合同
        if ( ! $result && Company::isChild($userid))
        {
            $parentUserid = Company::getparentUserid($userid);
            //总公司为集团版时、子公司为vip
            $result = self::isGroup($parentUserid);
        }

        return $result;
    }

    /**
     * 判断是否是vip正式会员、没有包含子公司的情况
     *
     * @param integer $userid
     * @return boolean
     */
    public static function isVip($userid)
    {
        $table = ContractDetail::tableName();
        $sql = "select * from {$table} as cd left join jdrcadmin.Contract as c on cd.ContractId = c.id where c.userId = {$userid} and cd.product =1 and cd.IsDel = 0 AND c.IsDel = 0 AND c.status = 1 AND c.startDate < GETDATE() AND DATEADD(DAY, 1, c.endDate) > GETDATE()";
        $result = ContractDetail::getDb()->createCommand($sql)->queryAll();
        return $result ? TRUE : FALSE;
    }

    /**
     * 判断是否为集团版
     *
     * @param integer $userid
     * @return boolean
     */
    public static function isGroup($userid)
    {
        $table = ContractDetail::tableName();
        $sql = "select * from {$table} as cd left join jdrcadmin.Contract as c on cd.ContractId = c.id where c.userId = {$userid} and cd.product =1 and cd.IsDel = 0 AND c.IsDel = 0 AND c.status = 1 AND c.startDate < GETDATE() AND DATEADD(DAY, 1, c.endDate) > GETDATE() AND cd.isGroup = 1";
        $result = ContractDetail::getDb()->createCommand($sql)->queryAll();
        return $result ? TRUE : FALSE;
    }
}