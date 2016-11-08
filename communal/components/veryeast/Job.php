<?php
namespace communal\components\veryeast;

use communal\components\Constant;
use communal\models\ve_company\Company;
use communal\models\ve_company\JobMatch;
use communal\models\ve_core\CoreCompanyResumeHistory;
use communal\models\ve_main\ApiJob;
use Yii;
use communal\components\BaseComponent;
use yii\base\InvalidConfigException;
use communal\models\df_resource\City;
use communal\models\df_resource\Province;
use communal\models\ve_company\Job as JobModel;
use communal\models\df_resource\Post;

class Job extends BaseComponent
{
    public  $job_id;

    public function init(){
        parent::init();
        if(empty($this->job_id)){
            throw new InvalidConfigException('Property job_id must be set!');
        }
    }

    /**
     * 获取职位信息
     * @return array
     */
    public function getJobInfo(){
        $job = JobModel::find()->where(['job_id' => $this->job_id])->one();
        if(!$job){
            return [];
        }
        //工作详情
        $jobDetail = $job->getDetail()->asArray()->one();
        return array_merge($job->toArray(),$jobDetail);
    }

    public function getDetailsCn(){
        $arr = [];
        $jobInfo = $this->getJobInfo();
        //职位类别名
        if(isset($jobInfo['job_post_number'])){
            $arr['postName'] = Post::find()->where(['post_number' =>$jobInfo['job_post_number']])->select('name')->one()->name;
        }else{
            $arr['postName'] = "未设置";
        }
//
        //企业名
        $arr['companyName'] = Company::find()->where(['c_userid'=>$jobInfo['c_userid']])->select('company_name')->one()->company_name;
        $arr['jobName'] = $jobInfo['job_name'];
        $arr['c_userid'] = $jobInfo['c_userid'];
        //学历
        $degreeMap = Yii::getConfig('degree', '@communal/config/data/company.php');
        $arr['degree'] = isset($degreeMap[$jobInfo['degree_id']])?$degreeMap[$jobInfo['degree_id']]:'不限';

        //工作地点
        $location = $jobInfo['work_place'];
        if(empty($location)){
            $arr['workPlace'] = '未设置';
        }else{
            $cityInfo = City::find()->where(['city_number' => $location])->select(['abbreviation','parent_id'])->one();
            if($cityInfo){//选择的为城市
                $province = Province::find()->where(['id' => $cityInfo->parent_id])->select('name')->one();
                $arr['workPlace'] =  $province->name .' - '.$cityInfo->abbreviation;
            }else{
                $province = Province::find()->where(['province_number' => $location])->select('name')->one();
                $arr['workPlace'] = $province->name;
            }

        }

        //工作年限
        $workYear = $jobInfo['work_year'];
        $workYearStringMap = Yii::getConfig('workYearString', '@communal/config/data/company.php');
        if(isset($workYearStringMap[$workYear])){
            $arr['workYear'] = $workYearStringMap[$workYear];
        }else{
            $arr['workYear'] = '不限';
        }

        //年龄
        if($jobInfo['age_min']==0){
            $arr['age'] = '不限';
        }else if($jobInfo['age_max'] ==0){
            $arr['age'] = $jobInfo['age_min'].'岁以上';
        }else{
            $arr['age'] = $jobInfo['age_min'].' - '.$jobInfo['age_max'];
        }

        //性别
        $arr['gender'] = $jobInfo['gender_id'] == 0?'不限':($jobInfo['gender_id'] ==1?'男':'女');

        //期望薪资
        $salary = $jobInfo['salary'];
        $salaryStringMap = Yii::getConfig('salaryString', '@communal/config/data/company.php');
        $arr['salary'] = isset($salaryStringMap[$salary])?$salaryStringMap[$salary]:'不限';

        //语言
        $languageArr = [];
        $languageStringMap = Yii::getConfig('languageString','@communal/config/data/person.php');
        $jobLanguageArr = str_split(strrev(base_convert($jobInfo['language_requirement'], 10, 8)));
        foreach($jobLanguageArr as $key => $language){
            if($language == 0){
                continue;
            }
            if(isset($languageStringMap[$key+1])){
                $languageArr[] = $languageStringMap[$key+1];
            }
        }
        $arr['language'] = $languageArr ? implode(',',$languageArr) : '不限';
        return $arr;
    }

    /**
     * @param int $day 有效期
     * @param array $except 排除的个人用户id
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getApplyPersons($day = 0,$except=[]){
        $query = CoreCompanyResumeHistory::find()->where('job_id = :job_id and is_apply = 1',[':job_id' => $this->job_id]);
        if($day > 0){
            $query = $query->andOnCondition('apply_time > "'.date('Y-m-d H:i:s',strtotime('-'.$day.'days')).'"');
        }
        if($except){
            $query = $query->andOnCondition('p_userid not in ('.implode(',',$except).')');
        }
        return $query->select('p_userid')->all();
    }

    public function isValid(){
        return  JobModel::find()->where(['job_id' => $this->job_id,'is_deleted' => 0,'is_recycler' => 0,'status' => 1])->one()?true:false;
    }

    public function isEQuestJob(){
        return ApiJob::find()->where([
            'job_id' => $this->job_id
        ])->exists();
    }

    public function getEQuestJobInfo(){
        return ApiJob::find()->where(['job_id'=> $this->job_id])->asArray()->one();
    }

    /**
     * 是否为急聘职位
     * @param $jobId
     * @return bool
     */
    public function isPressing($jobId){
        return JobModel::find()->where([
            'job_id' => $jobId,
            'is_emergency' =>1,
        ])->exists();

    }

    public function calcResumeMatchingDegree($userId){
        $jobMatchData = $this->getJobMatch();

        if(!$jobMatchData ||empty($jobMatchData)){
            return -1;
        }
        $jobData = $this->getJobInfo();
        if(!$jobData){
            return -3;
        }

        $person = Yii::getCommunalComponent('person',['p_userid' => $userId]);
        $desireData = $person->getDesiredInfoByUserId(['job', 'salary', 'location']);
        $personData = $person->getResumeFields();
        $totalScore = 0;
        $score = 0;

        if(isset($jobMatchData['post']) && $jobMatchData['post']){
            $totalScore += 10 * $jobMatchData['post'];
            $score = ( ( in_array($jobData['job_post_number'], $desireData['job']) )
                    ? 10  : 5 ) * $jobMatchData['post'];
        }

        //学历
        if( isset($jobMatchData['degree']) && $jobMatchData['degree']) {
            //真实的学历规则
            $rule = array(
                0 => 0,
                1 => 1,
                2 => 2,
                3 => 1,
                4 => 2,
                5 => 3,
                6 => 4,
                7 => 5,
                8 => 6
            );
            $totalScore += 10 * $jobMatchData['degree'];
            $personData['degree'] = isset($rule[$personData['person']['degree']]) ?
                $rule[$personData['person']['degree']] : 0;

            $jobData['degree_id'] = isset($rule[$jobData['degree_id']]) ?
                $rule[$jobData['degree_id']] : 0;

            if( $personData['person']['degree'] >= $jobData['degree_id'] ) {
                $score += 10 * $jobMatchData['degree'];
            } else {
                $score += (5 - max(0,min(5, $jobData['degree_id'] - $personData['person']['degree']))) * $jobMatchData['degree'];
            }
        }

        //工作年限
        if(isset($jobMatchData['work_year']) && $jobMatchData['work_year']) {
            $totalScore += 10 * $jobMatchData['work_year'];
            $score += ( $personData['person']['work_year'] >= $jobData['work_year'] ? 10 : 5 ) * $jobMatchData['work_year'];
        }

        //年龄
        if( isset($jobMatchData['age']) && $jobMatchData['age']) {

            $totalScore += 10 * $jobMatchData['age'];

            if($jobData['age_max'] == 0 && $jobData['age_min'] == 0) {
                $score += 10 * $jobMatchData['age'];
            } else {
                $age = floor((time() - strtotime($personData['person']['birthday'])) / 31557600);

                if($age >= $jobData['age_min'] && $age <= $jobData['age_max']) {
                    $score += 10 * $jobMatchData['age'];
                } else {
                    $offset = min(abs($age - $jobData['age_min']), abs($age - $jobData['age_max']));
                    $score += max(0, ( 1 - ceil($offset / 3) / 4 ) ) * 5  * $jobMatchData['age'];
                }
            }

        }

        //薪水

        if( isset($jobMatchData['salary']) && $jobMatchData['salary']) {

            $totalScore += 10 * $jobMatchData['salary'];

            if( $jobData['salary'] == 0 || $desireData['desired_salary'] == 0 ) {

                $score += 10 * $jobMatchData['salary'];

            } else {

                $desiredSalary         = $desireData['desired_salary'];
                $desiredSalaryMode     = $desireData['desired_salary_mode'];
                $desiredSalaryCurrency = $desireData['desired_salary_currency'];

                /**
                 * 以下是操蛋的汇率转换代码 请勿直视
                 */
                $desiredSalaryCurrencyArray = array(
                    1 => 1,
                    2 => Constant::EXCHANGE_RATE_USD,
                    3 => Constant::EXCHANGE_RATE_GBP,
                    4 => Constant::EXCHAGE_RATE_EUR
                );

                $desiredSalaryModeArray = array( 1 => 1, 2 => 12);

                $desiredSalaryArray = array(
                    0   => 0,
                    1   => 1000,
                    2   => 2000,
                    3   => 3000,
                    4   => 4500,
                    5   => 6000,
                    6   => 8000,
                    7   => 10000,
                    8   => 15000,
                    9   => 20000,
                    10  => 30000,
                    11  => 50000,
                    101 => 10000,
                    102 => 20000,
                    103 => 30000,
                    104 => 50000,
                    105 => 80000,
                    106 => 100000,
                    107 => 200000,
                    108 => 300000,
                    109 => 450000,
                    110 => 600000,
                    111 => 800000,
                    112 => 1000000
                );

                $salary = $desiredSalaryArray[$desiredSalary]
                    * $desiredSalaryCurrencyArray[$desiredSalaryCurrency]
                    / $desiredSalaryModeArray[$desiredSalaryMode];

                $newSalaryArray = array(
                    1  => 1000,
                    2  => 2000,
                    3  => 3000,
                    4  => 4000,
                    5  => 5000,
                    6  => 6000,
                    7  => 8000,
                    8  => 10000,
                    9  => 15000,
                    10 => 20000,
                    11 => 30000,
                    12 => 50000,
                    13 => 80000,
                    14 => 100000,
                    15 => 0xffffff
                );

                $salaryType = 13;
                foreach($newSalaryArray as $key => $val) {
                    if($salary <= $val) {
                        $salaryType = $key;
                        break;
                    }
                }

                if($jobData['salary'] >= $salaryType) {
                    $score += 10 * $jobMatchData['salary'];
                } else {
                    $score += 5 * $jobMatchData['salary'];
                }

            }

        }

        //工作地点
        if( isset($jobMatchData['work_place']) && $jobMatchData['work_place']) {

            $totalScore += 10 * $jobMatchData['work_place'];

            if($jobData['work_place'] === $personData['person']['current_location']) {
                $score += 10 * $jobMatchData['work_place'];
            }
        }

        //性别
        if( isset($jobMatchData['gender']) && $jobMatchData['gender'] ) {
            $totalScore += 10 * $jobMatchData['gender'];
            if($jobData['gender_id'] == 0 || $jobData['gender_id'] == $personData['person']['gender']) {
                $score += 10 * $jobMatchData['gender'];
            }
        }

        //婚姻
        if( isset($jobMatchData['marital_status']) && $jobMatchData['marital_status']) {
            $totalScore += 10 * $jobMatchData['marital_status'];

            //离异不算是未婚 保密全符合
            //婚姻状况0、不限 1、已婚 2、未婚'
            //1：未婚\r\n                        2：已婚\r\n                        3：离异\r\n                        4：保密（默认）
            if($jobData['marital_status'] == 0 || $personData['person']['marital'] == 4) {
                $score += 10 * $jobMatchData['marital_status'];
            } elseif($personData['person']['marital'] == 2 && $jobData['marital_status'] == 1) {
                $score += 10 * $jobMatchData['marital_status'];
            } elseif($personData['person']['marital'] == 1 && $jobData['marital_status'] == 2) {
                $score += 10 * $jobMatchData['marital_status'];
            } else {
                $score += 0;
            }

        }

        //经历
        if( isset($jobMatchData['special_experience']) && $jobMatchData['special_experience'] ) {

            $totalScore += 10 * $jobMatchData['special_experience'];
            //老版公司匹配不完善 容易出现大酒店 c_userid都为0的情况
            if($jobData['special_experience'] & 1) {
                $data = $person->getWorkHistory('c_userid');
                $ids  = array_map(function($val) { return $val['c_userid']; }, $data);
                $data = \communal\components\veryeast\Company::getInfoListByUserIds($ids, array('is_international'));
                $tmp  = 0;
                foreach($data as $val) {
                    if($val['is_international']) $tmp = 10 * $jobMatchData['special_experience'];
                }
                $score += $tmp;
            } else {
                $score += 10 * $jobMatchData['special_experience'];
            }
        }
        //语言
        if( isset($jobMatchData['language']) && $jobMatchData['language'] ) {
            $totalScore += 10 * $jobMatchData['language'];

            if($jobData['language_requirement'] == 0) {
                $score += 10 * $jobMatchData['language'];
            } else {
                $languageInfo = $person->getLanguageInfoByUserId();
                $arr          = str_split(strrev(base_convert($jobData['language_requirement'], 10, 8)));
                $tmp          = 0;

                foreach($arr as $key => $val) {

                    if($val == 0) continue;

                    if( isset($languageInfo[$key + 1]) ) {
                        if($languageInfo[$key + 1] >= $val) {
                            $tmp = 10 * $jobMatchData['language'];
                            break;
                        } else {
                            $temp = (1 - ($val - $languageInfo[$key + 1]) / 4) * 5 * $jobMatchData['language'];
                            if($temp >= $tmp) $tmp = $temp;
                        }
                    }
                }

                $score += $tmp;
            }


        }

        return sprintf('%.2f', $totalScore > 0 ? ($score / $totalScore * 100) : -1);

    }

    /**
     * 获取职位匹配度
     * @return mixed
     */
    public function getJobMatch(){
        $matchModel = JobMatch::find()->where([
            'job_id' => $this->job_id
        ])->one();
        $matches = $matchModel ? $matchModel->match_detail : JObmatch::DEFAULT_VALUE;
        return json_decode($matches,true);
    }

}