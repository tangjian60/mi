<?php

namespace communal\components\veryeast;

use communal\models\ve_company\CompanyChildRelation;
use Yii;
use communal\components\BaseComponent;
use yii\base\InvalidConfigException;
use communal\models\ve_company\Company as CompanyModel;
use communal\components\helper\ArrayHelper;
/**
 * Component component
 */
class Company extends BaseComponent
{
    public $c_userid;
    public function init(){
        parent::init();
        if(empty($this->c_userid)){
            throw new InvalidConfigException('Property job_id must be set!');
        }
    }

    public function getInfo($param = []){
        $query = CompanyModel::find()->where(['c_userid'=>$this->c_userid]);
        if($param){
            $query->select($param);
        }
        return $query->asArray()->one();
    }

    public static function getInfoListByUserIds(array $ids, array $params = array()){
        if(empty($ids)){
            return null;
        }
        $result = CompanyModel::find()->where(['c_userid' => $ids])->asArray()->all();
        $result = ! empty($result) ? ArrayHelper::toHashMap($result, 'c_userid') : $result;
        return $result;
    }

    /**
     * 判断是否是子公司
     *
     * @param $child_company_userid
     * @param null $c_userid
     * @internal param int $userid
     * @return bool
     */
    public static function isChild($child_company_userid, $c_userid = NULL)
    {
        return CompanyChildRelation::find()->where([
            'child_company_userid' =>$child_company_userid,
            'c_userid' => $c_userid,
            'is_deleted' => 0
        ])->exists();

    }

    public static function getParentUserid($c_userid){
        $model = CompanyChildRelation::find()->select('c_userid')->where([
            'child_company_userid' => $c_userid,
            'is_deleted' => 0
        ])->one();
        return $model ? $model->c_userid:0;

    }

}
