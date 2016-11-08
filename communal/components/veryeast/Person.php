<?php

namespace communal\components\veryeast;

use communal\components\ErrorConstant;
use communal\components\I18N;
use communal\components\resource\Area as AreaApi;
use communal\components\resource\Task;
use communal\models\ve_core\CoreCompanyResume;
use communal\models\ve_core\CoreCompanyResumeHistory;
use communal\models\ve_core\CoreFocusRelation;
use communal\models\ve_person\PersonDesiredJob;
use communal\models\ve_person\PersonDesiredLocation;
use communal\models\ve_person\PersonDesiredPosition;
use communal\models\ve_person\PersonFavoriteJob;
use communal\models\ve_person\PersonInfo;
use communal\models\ve_person\PersonResumeCoverLetter;
use communal\models\ve_person\PersonResumeLanguage;
use communal\models\ve_person\PersonResumeWorkHistory;
use communal\models\ve_stat\StatCompanyLog;
use Pheanstalk\Pheanstalk;
use Yii;
use communal\components\BaseComponent;
use communal\components\Exception;
use yii\base\InvalidConfigException;
use communal\models\ve_person\Person as PersonModel;
use communal\models\ve_person\PersonService;
use communal\models\ve_person\PersonServiceValueAdded;
use communal\models\ve_company\Job as JobModel;
use communal\models\df_resource\Post;
use communal\models\df_resource\City;
use communal\models\df_resource\Province;

/**
 * Person component
 */
class Person extends BaseComponent
{
    public $p_userid = 0;

    //const SERVICE_COMPETITIVE = 2;

    public function __construct($config = [])
    {
        $this->p_userid = Yii::$app->user->id;
        parent::__construct($config);

        if (empty($this->p_userid)) {
            throw new InvalidConfigException('Not Login, or property p_userid must be set!');
        }
    }

    public function personInfo()
    {
        return PersonModel::find()->where(['user_id' => $this->p_userid])->asArray()->one();
    }

    public function field($field)
    {
        return $this->personInfo()[$field];
        //Person::find()->where(['p_userid' => $this->p_userid])->asArray()->one();
    }

    public function serviceInfo()
    {
        return PersonService::find()->where(['p_userid' => $this->p_userid])->all();
    }

    public function serviceList()
    {
        return PersonServiceValueAdded::find()->all();
    }

    /**
     * 获取简历信息
     * @param array $fields
     * @return array
     */
    public function getResumeFields(array $fields = [])
    {
        $arr = [];
        $person = PersonModel::find()->where(['user_id' => $this->p_userid])->one();
        if(!$person){
            return [];
        }
        foreach ($fields as $field) {
            $method = 'get'.ucfirst($field);
            if (method_exists($person, $method)) {
                $arr[$field] = $person->$method()->asArray()->all();
            }
        }
        return array_merge([
            'person' => $person->toArray()],
            $arr
        );
    }

    /**
     * 简历与职位匹配度
     * @param $job_id
     * @return array 匹配度信息
     */
    public function resumeMatchJob($job_id)
    {
        $matches = [];
        //职位信息
        $jobInfo = Yii::getCommunalComponent('job', ['job_id' => $job_id],true)->getJobInfo();
        $personInfo = $this->getResumeFields(['desiredPosition', 'desiredLocation', 'desiredJob', 'resumeLanguage']);
        if (empty($jobInfo) || empty($personInfo)) {
            return [];
        }
        //匹配职位
        $personPositions = isset($personInfo['desiredPosition'])?$personInfo['desiredPosition']:null;
        $jobPosition = $jobInfo['job_post_number'];
        $matches['position'] = 0;
        //同级匹配
        $isParentMatch = false;
        foreach($personPositions as $personPosition){
            if($jobPosition == $personPosition['position']){
                $matches['position'] = 100;
                $isParentMatch = true;
                break;
            }
        }
        //父级匹配
        if(!$isParentMatch){
            foreach($personPositions as $personPosition){
                if((substr($jobPosition, 0, 2) == substr($personPosition['position'], 0, 2))){
                    $matches['position'] =20;
                    break;
                }
            }
        }

        //匹配学历
        $personDegree = isset($personInfo['person']['degree']) ? $personInfo['person']['degree'] : 0;
        $jobDegree = isset($jobInfo['degree_id']) ? $jobInfo['degree_id'] : 0;
        $matches['degree'] = $personDegree >= $jobDegree ? 100 : 0;

        //匹配工作地点
        $personWorkPlace = isset($personInfo['desiredLocation'][0]['location']) ? $personInfo['desiredLocation'][0]['location'] : null;
        $jobWorkPlace = $jobInfo['work_place'];
        if ($jobWorkPlace == $personWorkPlace) {
            $matches['workPlace'] = 100;
        } else if (substr($jobWorkPlace, 0, 2) == substr($personWorkPlace, 0, 2)) {
            $matches['workPlace'] = 20;
        } else {
            $matches['workPlace'] = 0;
        }

        //匹配工作年限
        $personWorkYear = isset($personInfo['person']['work_year']) ? $personInfo['person']['work_year'] : 0;
        $jobWorkYear = isset($jobInfo['work_year']) ? $jobInfo['work_year'] : 0;
        $matches['workYear'] = $personWorkYear >= $jobWorkYear ? 100 : 0;

        //匹配年龄
        $personAge = isset($personInfo['person']['birthday']) ? date('Y') - date('Y', strtotime($personInfo['person']['birthday'])) : 0;
        $jobAgeMin = $jobInfo['age_min'];
        $jobAgeMax = $jobInfo['age_max'] == 0 ? 9999 : $jobInfo['age_max'];//相当于无限大
        $matches['age'] = $personAge >= $jobAgeMin && $personAge <= $jobAgeMax ? 100 : 0;

        //匹配性别
        $personGender = $personInfo['person']['gender'];
        $jobGender = $jobInfo['gender_id'];
        $matches['gender'] = $jobGender == 0 ? 100 : ($jobGender == $personGender ? 100 : 0);

        //匹配薪水,币种转化为人民币/月
        $personSalaryMap = Yii::getConfig('personSalary', '@communal/config/data/person.php');
        $personSalaryMin = isset($personInfo['desiredJob'][0]['desired_salary']) ? $personSalaryMap[$personInfo['desiredJob'][0]['desired_salary']]['min'] : 0;
        $personSalaryMax = isset($personInfo['desiredJob'][0]['desired_salary']) ? $personSalaryMap[$personInfo['desiredJob'][0]['desired_salary']]['max'] : 99999999;

        //汇率
        $currencyRateMap = Yii::getConfig('currencyRate', '@communal/config/data/person.php');
        if (isset($currencyRateMap[$personInfo['desiredJob'][0]['desired_salary_currency']])) {
            $personSalaryMin *= $currencyRateMap[$personInfo['desiredJob'][0]['desired_salary_currency']];
            $personSalaryMax *= $currencyRateMap[$personInfo['desiredJob'][0]['desired_salary_currency']];
        }
        //年薪转月薪
        if(isset($personInfo['desiredJob'][0]['desired_salary_mode']) && $personInfo['desiredJob'][0]['desired_salary_mode'] == 2){
            $personSalaryMin /= 12;
            $personSalaryMax /=12;
        }
        //职位薪资
        $jobSalaryMap = Yii::getConfig('salary', '@communal/config/data/company.php');
        $jobSalaryMin = isset($jobSalaryMap[$jobInfo['salary']]) ? $jobSalaryMap[$jobInfo['salary']]['min'] : 0;
        $jobSalaryMax = isset($jobSalaryMap[$jobInfo['salary']]) ? $jobSalaryMap[$jobInfo['salary']]['max'] : 99999999;
//        var_dump($personSalaryMin,$personSalaryMax,$jobSalaryMin,$jobSalaryMax);die;
        if ($jobInfo['salary'] == 0 || ($personSalaryMin >= $jobSalaryMin && $personSalaryMax <= $jobSalaryMax)) {//面议
            $matches['salary'] = 100;
        } else if (($personSalaryMin <= $jobSalaryMin && $personSalaryMax >= $jobSalaryMin) || ($personSalaryMin <= $personSalaryMax && $personSalaryMax >= $personSalaryMax)) {
            $matches['salary'] = 50;
        } else {
            $matches['salary'] = 0;
        }

        //匹配语言能力,逻辑：简历有职位要求的所有语言，为50分，若每项语言熟练度都高于（等于）职位要求，得100，其他情况0
        $jobLanguageArr = str_split(strrev(base_convert($jobInfo['language_requirement'], 10, 8)));
        $personLanguageArr = [];
        foreach ($personInfo['resumeLanguage'] as $value) {
            $personLanguageArr[$value['language']] = $value['ability'];
        }
        $matches['language'] = 100;
        foreach ($jobLanguageArr as $key => $value) {

            if ($value == 0) {//等于0为没设置
                continue;
            }
            if (isset($personLanguageArr[$key + 1])) {
                if ($personLanguageArr[$key + 1] < $value) {
                    $matches['language'] = 50;
                } else {
                    continue;
                }

            } else {
                $matches['language'] = 0;
                break;
            }
        }
        $matches['all'] = ($matches['position'] + $matches['degree'] + $matches['workPlace'] + $matches['workYear']+$matches['age'] + $matches['gender'] + $matches['salary'] + $matches['language']) * 0.125;

        //中文推荐语
        $suggestWordCns = Yii::getConfig('matchingSuggestCn', '@communal/config/data/service.php');
        $matches['suggestWordCn'] = $matches['all'] >= 90 ? $suggestWordCns[0] : ($matches['all'] >= 80 ? $suggestWordCns[1] : ($matches['all'] >= 60 ? $suggestWordCns[2] : $suggestWordCns[3]));
        return $matches;

    }

    /**
     * 获取与用户匹配度高的职位（by post and location）
     * @param int $num
     */
    public function getRecommendJobs($num = 8){
        $personInfo = $this->getResumeFields(['desiredPosition','desiredLocation']);
        $desirePosition = isset($personInfo['desiredPosition'][0]['position'])?$personInfo['desiredPosition'][0]['position']:'';
//        $desireLocation = isset($personInfo['desiredLocation'][0]['location'])?$personInfo['desiredLocation'][0]['location']:'';
        if(!$desirePosition){
            return [];
        }
        $query = JobModel::find()->where(['is_deleted' => 0,'status'=>'1','is_recycler' => 0])->andOnCondition('job_post_number like :post',[':post'=>'%'.$desirePosition.'%'])->limit(100)->orderBy('modify_time desc');
//        if($desireLocation){
//            $query->andOnCondition('work_place like :work_place',[':work_place'=> '%'.$desireLocation.'%']);
//        }
        $jobs = $query->all();
        $matchResults = [];
        foreach($jobs as $job){
            $tmp  = $this->resumeMatchJob($job->job_id);
            $tmp['job_id']  = $job['job_id'];
            if($tmp['all'] >= 60){
                $matchResults[] = $tmp;
            }
        }
        uasort($matchResults,function($match1,$match2){
            if($match1['all'] == $match2['all']){
                return 0;
            }else{
                return ($match1['all'] < $match2['all'])?1:-1;
            }
        });
        $recommendJobs =  array_slice($matchResults,0,$num);
        unset($matchResults);
        return  array_map(function($arr){
            $jobComponent = Yii::getCommunalComponent('job', ['job_id' => $arr['job_id']]);
            $jobComponent->job_id = $arr['job_id'];
            $jobInfoCn = $jobComponent->getDetailsCn();
            return array_merge($jobInfoCn,[
                'job_id' => $arr['job_id'],
                'match' => $arr['all'],
            ]);
        },$recommendJobs);
//        var_dump($recommendJobs);die;
//        var_dump($jobs);
    }

    /**
     * 获取页面显示的简历信息
     * @return array
     */
    public function getDetailsCn($job_id){
        $jobInfo = Yii::getCommunalComponent('job', ['job_id' => $job_id],true)->getJobInfo();
        $arr = [];
        $personInfo = $this->getResumeFields(['desiredPosition', 'desiredLocation', 'desiredJob', 'resumeLanguage']);
        $postId = isset($personInfo['desiredPosition'][0]['position'])?$personInfo['desiredPosition'][0]['position']:null;
        //匹配职位
        $personPositions = isset($personInfo['desiredPosition'])?$personInfo['desiredPosition']:null;
        $jobPosition = $jobInfo['job_post_number'];
        $matches['position'] = 0;
        //同级匹配
        $isParentMatch = false;
        foreach($personPositions as $personPosition){
            if($jobPosition == $personPosition['position']){
                $postId = $personPosition['position'];
                $isParentMatch = true;
                break;
            }
        }
        //父级匹配
        if(!$isParentMatch){
            foreach($personPositions as $personPosition){
                if((substr($jobPosition, 0, 2) == substr($personPosition['position'], 0, 2))){
                    $postId = $personPosition['position'];
                    break;
                }
            }
        }
        //职位
        if($postId){
            $posts = Yii::getConfig('ft_new', '@communal/config/data/post.php');
            $arr['postName'] =  isset($posts[$postId])?$posts[$postId]:'未设置';
        }else{
            $arr['postName'] = '未设置';
        }

        //学历
        $degreeMap = Yii::getConfig('degree', '@communal/config/data/company.php');
        $arr['degree'] = isset($degreeMap[$personInfo['person']['degree']])?$degreeMap[$personInfo['person']['degree']]:'未设置';

        //工作地点
        $location = isset($personInfo['desiredLocation'][0]['location'])?$personInfo['desiredLocation'][0]['location']:null;
        if($location){
            $cityInfo = City::find()->where(['city_number' => $location])->select(['abbreviation','parent_id'])->one();
            if($cityInfo){//城市
                $province = Province::find()->where(['id' => $cityInfo->parent_id])->select('name')->one();
                $arr['workPlace'] =  $province->name .' - '.$cityInfo->abbreviation;
            }else{//省份
                $province = Province::find()->where(['province_number' => $location])->select('name')->one();
                $arr['workPlace'] =  $province->name;
            }


        }else{
            $arr['workPlace'] = '未设置';
        }

        //工作经验
        $arr['workYear'] = $personInfo['person']['work_year']?$personInfo['person']['work_year'].'年':'不限';

       // 年龄
        $arr['age'] = isset($personInfo['person']['birthday']) ? (date('Y') - date('Y', strtotime($personInfo['person']['birthday']))).'岁' : '未设置';

        //性别
        $arr['gender'] = $personInfo['person']['gender'] == 0?'未设置':($personInfo['person']['gender'] ==1?'男':'女');

        //期望薪资
        $personSalaryStringMap = Yii::getConfig('salaryString', '@communal/config/data/person.php');
        if(isset($personInfo['desiredJob'][0]['desired_salary_mode'])){
            if($personInfo['desiredJob'][0]['desired_salary_mode'] == 2 && $personInfo['desiredJob'][0]['desired_salary']>0){
                $arr['salary'] = '年薪 ';
            }else{
                $arr['salary'] = '';
            }

            if(isset($personSalaryStringMap[$personInfo['desiredJob'][0]['desired_salary']])){
                $arr['salary'].=$personSalaryStringMap[$personInfo['desiredJob'][0]['desired_salary']];
            }
            $currency = $personInfo['desiredJob'][0]['desired_salary_currency'];
            $currencyStringMap =Yii::getConfig('currencyString', '@communal/config/data/person.php');
            if($personInfo['desiredJob'][0]['desired_salary']!= 0 && isset($currencyStringMap[$currency])){
                $arr['salary'].=' '.$currencyStringMap[$currency];
            }
        }else{
            $arr['salary'] = '面议';
        }

        //语言
        $language = [];
        $languageStringMap = Yii::getConfig('languageString','@communal/config/data/person.php');
        if(isset($personInfo['resumeLanguage'])){
            foreach($personInfo['resumeLanguage'] as $l){
                if(isset($languageStringMap[$l['language']])){
                    $language[] = $languageStringMap[$l['language']];
                }
            }
        }
        $arr['language'] = $language ? implode(',',$language) : '未设置';

        return $arr;
    }

    public function getCollectList(){
        $list = PersonFavoriteJob::find()->select('job_id')->where([
            'p_userid' => $this->p_userid,
            'is_deleted' => '0'
        ])->asArray()->all();
        $result = [];
        foreach($list as $item){
            $result[] = $item['job_id'];
        }
        return $result;

    }

    /**
     *竞争力信息
     * @param $job_id
     */
    public function getCompetitiveInfo($job_id){
        $job = Yii::getCommunalComponent('job', ['job_id' => $job_id],true);
        $applyUsers = $job->getApplyPersons(14);
        $totalApplyUser = count($applyUsers);
        //没人申请则直接返回
        if($totalApplyUser <= 0){
            return [];
        }

        $applyUserInfo = array_map(function($user){
            $person = Yii::getCommunalComponent('person', ['p_userid' => $user->p_userid],true)->getResumeFields(['desiredPosition', 'desiredLocation', 'desiredJob', 'resumeLanguage']);
            return $person;
        },$applyUsers);
        $pool = $this->getCompetitivePool($applyUserInfo);//职位投递数据池
        $userInfo = $this->getResumeFields(['desiredPosition', 'desiredLocation', 'desiredJob', 'resumeLanguage']);
        $userPoolIndexes = $this->getPoolIndexes($userInfo);
        $competitiveRank = $this->getCompetitiveRank($userPoolIndexes,$pool);
        //排名比例
        $scale = round($competitiveRank['average']/($totalApplyUser),2);
        return compact('pool','competitiveRank','totalApplyUser','suggestWordCn','scale');
    }

    /**
     * 获取已投递职位数量
     */
    public function getApplyJobCount($date){
        $query = StatCompanyLog::find()->where(['p_userid' => $this->p_userid,'oper_type' => 2]);
        if($date){
            $query->andWhere(['>','add_time',$date]);
        }
        return $query->count();
    }

    public function applyJob($jobId, $lang = 0, $letterId = 0, $clientId = 0){
        ignore_user_abort(TRUE);

        $jobId = (int)$jobId;
        $userId = (int)$this->p_userid;
        $jobData = Yii::getCommunalComponent('job',['job_id' => $jobId])->getJobInfo();
        $lang = min(max($lang, 0), 3);

        //职位是否存在
        if(!$jobData){
            throw new Exception('',ErrorConstant::VEC_JOB_NOT_EXISTS);
        }

        //职位是否有效
        if($jobData['is_recycler'] == 1 || $jobData['is_deleted'] ==1 || $jobData['status'] != 1){
            throw new Exception('',ErrorConstant::VEC_JOB_NOT_VALID);
        }
        //是否过期
        if( strtotime($jobData['modify_time']) + ($jobData['job_indate'] * 86400) < time() ){
            throw new Exception('', ErrorConstant::VEC_JOB_EXPIRED);
        }
        $companyId = $jobData['c_userid'];
        $company = Yii::getCommunalComponent('company',['c_userid' => $companyId]);
        $companyData = $company->getInfo(['add_time','company_name']);
        $person = Yii::getCommunalComponent('person',['p_userid' => $this->p_userid]);
        $userData = $person->getResumeFields(['contact']);
        $area = Yii::getCommunalComponent('area');
        $cityData = $area->getCityDataByNum($jobData['work_place']);

        if($lang == 0 ){
            $lang = $userData['person']['default_resume']+1;
        }
        //判断个人是否有填写联系方式
        if(trim($userData['person']['true_name_cn']) ==='' || trim($userData['contact'][0]['mobile']) === '' && trim($userData['contact'][0]['telephone']) === ''){
            throw new Exception('',ErrorConstant::VEP_CONCACT_INFO_NOT_COMPLETE);
        }
        //判断个人是否被企业加入黑名单
        if($this->isPersonInBlacklist($companyId)){
            throw new Exception('',ErrorConstant::VEP_RESUME_NOT_MATCH);
        }
        //是否在一个月内向企业投递了5次简历 个人追踪器
        $count = StatCompanyLog::find()->where([
            'c_userid' => $companyId,
            'p_userid' => $userId,
            'oper_type' =>2
        ])->andWhere(['>','add_time',date('Y-m-d H:i:s', strtotime('-1month'))])->count();
        if($count > 5){
            throw new Exception(I18N::t(ErrorConstant::VEP_REACHED_MAX_APPLY_COUNT_PER_COMPANY, 5), ErrorConstant::VEP_REACHED_MAX_APPLY_COUNT_PER_COMPANY);
        }
        //每天20个限额

        $lastMonthApplyCounts = StatCompanyLog::find()->where([
            'c_userid' => $companyId,
            'oper_type' => 2
        ])->andWhere(['>','add_time',date('Y-m-d 00:00:00')])->count();
        if($lastMonthApplyCounts >=20){
            throw new Exception(I18N::t(ErrorConstant::VEP_REACHED_MAX_APPLY_COUNT_PER_DAY, 20), ErrorConstant::VEP_REACHED_MAX_APPLY_COUNT_PER_DAY);
        }
        //如果是特殊的eQuestJob,抛出错误
        $job = Yii::getCommunalComponent('job',['job_id' => $jobId]);
        if($job->isEQuestJob()){
            $tmp = $job->getEQuestJobInfo();
            throw new Exception(json_encode([
                'job_name' => $jobData['job_name'],
                'job_id'   => $jobId,
                'url'      => $tmp['redirect_url']
            ]),ErrorConstant::VEC_JOB_IS_EQUEST);
        }

        //一个月内不能投递重复的职位
        $historyData = CoreCompanyResumeHistory::find()->where([
            'p_userid' => $userId,
            'job_id' => $jobId
        ])->one();

        if($historyData && (strtotime($historyData['apply_time'])+ (86400 * 30)) > time()){
            throw new Exception('', ErrorConstant::VEP_TRIGGER_SINGLE_APPLY_DURING_ONE_MONTH);
        }
        //获取求职信信息
        if((int) $letterId !== 0){
            $letterData = PersonResumeCoverLetter::find()->where([
                'id' => $letterId,
                'user_id' => $userId])->one();
            if(!$letterData){
                $letterData = new PersonResumeCoverLetter();
            }
        }else{
            $letterData = new PersonResumeCoverLetter();
        }

        if($historyData) {
            $isNewApply = false;
            $historyDataBackup = $historyData->attributes;

            $historyData->letter_view_time = null;
            $historyData->apply_num = $historyData->apply_num + 1;
            $historyData->is_view = 0;
            $historyData->resume_view_time = null;
            $historyData->is_apply =1;
            $historyData->fore_apply_time = $historyData->apply_time;
        }else{
            $historyData = new CoreCompanyResumeHistory();
            $isNewApply = true;
            $historyData->c_userid = $companyId;
            $historyData->p_userid = $userId;
            $historyData->job_id = $jobId;
        }

        $historyData->apply_client_id      = $clientId;
        $historyData->primary_sieve_status = 1;
        $historyData->primary_sieve        = '';
        $historyData->person_delete        = 1;
        $historyData->company_delete       = 1;

        $historyData->company_job_name = $jobData['job_name'];
        $historyData->apply_time       = date('Y-m-d H:i:s');
        $historyData->is_reserve_job   = $jobData['is_reserve_job'];

        if(trim($letterData->content) !== ''){
            $historyData->letter_title = $letterData->title;
            $historyData->letter_content = $letterData->content;
        }

        $singleData = CoreCompanyResume::find()->where([
            'p_userid' => $userId,
            'c_userid' => $companyId,
        ])->one();

        if($singleData){
            $singleData->add_resume_time       = date('Y-m-d H:i:s');
            $singleData->person_delete         = 0;
            $singleData->person_delete_time    = NULL;
            $singleData->company_delete        = 0;
            $singleData->company_delete_time   = NULL;
            $singleData->is_apply              = 1;
            $singleData->is_view               = 0;
            $singleData->resume_last_view_time = $singleData->resume_view_time;
            $singleData->resume_view_time      = NULL;
            $singleData->is_reply              = 0;
            $singleData->reply_time            = NULL;
            $singleData->is_recycler_person    = 0;
            $singleData->is_recycler           = 0;
            $singleData->recycler_add_time     = NULL;
        }else{
            $singleData = new CoreCompanyResume();
            $singleData->c_userid = $companyId;
            $singleData->p_userid = $userId;
            $singleData->favorite_category_id = 0;
        }
        $singleData->job_id                 = $jobId;
        $singleData->company_job_name       = $jobData['job_name'];
        $singleData->company_name           = $companyData['company_name'];
        $singleData->p_name                 = $userData['person']['true_name_cn'];
        $singleData->p_name_english         = $userData['person']['true_name_en'];
        $singleData->gender_id              = $userData['person']['gender'];
        $singleData->degree_id              = $userData['person']['degree'];
        $singleData->birthday               = $userData['person']['birthday'];
        $area = Yii::getCommunalComponent('area');
        $singleData->current_location       = $area->getAreaDetailByNum($userData['person']['current_location'], TRUE, ' ');
        $singleData->current_location_code  = $userData['person']['current_location'];
        $singleData->domicile_location_code = $userData['person']['domicile_location'];
        $singleData->work_year              = $userData['person']['work_year'];

        $location = $this->getDesiredInfoByUserId( 'location');
        $singleData->desired_location_code = $this->makeDesiredLocationCode($location['location']);
        unset($location);

        /**
         * 如果不是新的申请 那么简历语言保留一致
         */
        if($isNewApply){
            $singleData->resume_language_id = $lang;
        }

        $singleData->is_photo = (int) (trim($userData['person']['photo']) !== '');
        $singleData->is_resume_english     = (int) (trim($userData['person']['true_name_en']) !== '');
        $singleData->company_register_time = $companyData['add_time'];
        $singleData->is_pressing_job       = (int)$job->isPressing($jobId);
        $singleData->is_reserve_job        = $jobData['is_reserve_job'];
        $singleData->job_city              = empty($cityData) ? '' : $cityData['abbreviation'];
        $singleData->job_city_id           = empty($cityData) ? 0 : $cityData['id'];

        /**
         * 初选状态的判断 初选状态去掉了
         */
        $singleData->primary_sieve_status  = 1;
//        if(!$historyData->save()) {
//            //var_dump($historyData->errors);
//            throw new Exception('投递失败，保存数据失败.', 0);
//        }
//
//        //模仿事务
//        if(!$singleData->save()){
//            if($isNewApply){
//                $historyData->delete();
//            }else{
//                $historyData->attributes = $historyDataBackup;
//                $historyData->save();
//            }
//            throw new Exception('投递失败，保存数据失败.', 0);
//        }

        /**
         * 统计API 放入任务队列 包含添加雇主点评
         * 如果队列服务挂了 那么会在当前进行计算
         */
        $production   = [
          'userId' => $userId,
            'companyId'   => $companyId,
            'jobId'       => $jobId,
            'companyName' => $companyData['company_name'],
            'personName'  => $userData['person']['true_name_cn'],
            'jobName'     => $jobData['job_name'],
            'language' => $lang,
            'email' => $userData['person']['email']
        ];
        if(!Task::add('Stat.applyJob',$production)){
            $degree = $job->calcResumeMatchingDegree($userId);
            //统计
            Stat::applyJob($userId, $companyId, $jobId);
            Stat::addEmployerVote($companyId, $production['companyName'], $userId, $production['personName'], $jobId, $production['jobName']);

            //核心表也是职位一致 否则容易出错
            CoreCompanyResume::updateAll(['matching_degree'=> $degree,],[
                'p_userid' =>$userId,
                'job_id' => $jobId
            ]);
            CoreCompanyResumeHistory::updateAll(['matching_degree'=> $degree,],[
                'p_userid' =>$userId,
                'job_id' => $jobId
            ]);

            //追踪简历
            $tracker = Yii::getCommunalComponent('tracker');
            $tracker->apply($companyId, $userId, $jobId, $production['jobName'], $production['companyName']);
            unset($data);
        }
        if($isNewApply){
            $this->updateCounterByUserId('apply_counts');
        }

        return true;



    }

    /**
     * 根据用户ID更新用户计数器
     *
     * @param  integer  $userId 用户ID
     * @param  string/array  $counterName 计数器名称
     * @param  integer $step 步进
     * @return integer 影响行数
     */
    public  function updateCounterByUserId($counterName, $step = 1)
    {

        if(is_array($counterName)) {
            $data = array();
            foreach($counterName as $val){
                $data[$val] = $step;
            }
        } else {
            $data = [$counterName => $step];
        }

        try {
//            return PersonInfoModel::model()->updateCounters(
//                $data, 'user_id=:id', array(':id' => (int)$userId)
//            );
            return PersonInfo::updateAllCounters($data,[
                'user_id' => $this->p_userid
            ]);
        } catch (Exception $e) {
            return FALSE;
        }
    }

    public  function getDesiredInfoByUserId($type = 'salary'){
        if(is_string($type)){
            $type = array($type);
        }
        $result = [];
        if(in_array('salary',$type)){
            $tmp = PersonDesiredJob::find()->where([
                'user_id' => $this->p_userid
            ])->one();
            $result = $tmp? $tmp->attributes: [];
        }

        if(in_array('location',$type)){
            $tmp = PersonDesiredLocation::find()->select('location')->where([
                'user_id' => $this->p_userid
            ])->asArray()->all();
            $location =  array_map(function($v){
                return $v['location'];
            },$tmp);
            $result['location'] = $location;
        }

        if(in_array('job',$type)){
            $tmp = PersonDesiredPosition::find()->select('position')->where([
                'user_id' => $this->p_userid
            ])->asArray()->all();
            $job = array_map(function($v){ return $v['position']; }, $tmp);
            $result['job'] = $job;
        }

        unset($result['modify_time']);
        unset($result['add_time']);
        unset($result['user_id']);
        return $result;
    }

    /**
     * 生成期望工作地点
     * @param array $locations 用逗号分割
     *
     * @return string
     */
    public   function makeDesiredLocationCode(array $locations = array()){

        if(empty($locations)){
            return '';
        }

        $provice_code = '';

        foreach($locations as $item){
            if(substr($item, -4) != '0000'){
                $str = substr($item, 0, 2);
                $provice_code = $str. '0000';

                if(!in_array($provice_code, $locations)){
                    array_push($locations, $provice_code);
                }

            }
        }

        return  implode(',', $locations);
    }

    /**
     * 是否为某企业粉丝
     * @param $companyId
     * @return bool
     */
    public function isFans($companyId){
        return CoreFocusRelation::find()->where([
            'p_userid' => $this->p_userid,
            'c_userid' => $companyId
        ])->exists();
    }

    public function addFavorite($jobId){
        $oldCollect = PersonFavoriteJob::find()->where([
            'p_userid' => $this->p_userid,
            'is_deleted' => 0,
            'job_id' => $jobId
        ])->one();

        if($oldCollect){
            throw new Exception('',11101);
        }

        $jobApi = Yii::getCommunalComponent('job',['job_id' => $jobId]);
        $jobData = $jobApi->getJobInfo();
        if(!$jobData){
            throw new Exception('',21001);
        }

        $status = is_array($jobData) && $jobData['is_recycler'] == 0
            && $jobData['is_deleted'] == 0
            && $jobData['status'] ==1
            && strtotime($jobData['modify_time']) +(86400 * $jobData['job_indate']) > time();
        if(! $status){
            throw new Exception('',21002);
        }

        $companyApi = Yii::getCommunalComponent('company',['c_userid' => $jobData['c_userid']]);
        $companyData = $companyApi->getInfo(['company_name']);
        $companyName = isset($companyData['company_name']) ? $companyData['company_name'] : '';
        $areaApi = Yii::getCommunalComponent('area');
        $cityData = $areaApi->getCityDataByNum($jobData['work_place']);

        $model = new PersonFavoriteJob();
        $model->p_userid = $this->p_userid;
        $model->c_userid = $jobData['c_userid'];
        $model->company_job_name = $jobData['job_name'];
        $model->company_name = $companyName;
        $model->job_id = $jobId;
        $model->job_province_id = (int)substr($jobData['work_place'],0,2);
        $model->job_city_id = (int)$cityData['id'];

        if($model->save()){
            Stat::favoriteJob($this->p_userid,$jobData['c_userid'],$jobId);
            $this->updateCounterByUserId('favorite_counts',1);
            return (int)$model->id;
        }else{
            return false;
        }

    }

    private function isPersonInBlacklist($c_userid){
        return CoreCompanyResume::find()->where(['p_userid' => $this->p_userid,'c_userid'=> $c_userid,'is_blacklist'=>1])->exists();

    }

    private  function getCompetitivePool($applyUserInfo){
        $pool = [
            'workYear'=>[0=>0,1=>0,2=>0,3=>0,4=>0,5=>0],
            'age'=> [0=>0,1=>0,2=>0,3=>0,4=>0],
            'degree' => [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0],
            'salary' => [0=>0,1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0],
        ];
        foreach($applyUserInfo as $person){
            //工作经验
            $indexArr =  $this->getPoolIndexes($person);
            $pool['workYear'][$indexArr['workYear']] += 1;
            $pool['age'][$indexArr['age']] += 1;
            $pool['degree'][$indexArr['degree']] += 1;
            $pool['salary'][$indexArr['salary']] += 1;
        }
        return $pool;
    }

    private function getPoolIndexes(array $personInfo){
        //工作经验
        $indexArr = [];
        $workYear = isset($personInfo['person']['work_year'])?$personInfo['person']['work_year']:0;
        if($workYear >= 10){
            $indexArr['workYear'] = 5;
        }elseif($workYear >= 7){
            $indexArr['workYear'] = 4;
        }elseif($workYear >= 5){
            $indexArr['workYear'] = 3;
        }elseif($workYear >= 3){
            $indexArr['workYear'] = 2;
        }elseif($workYear >=1){
            $indexArr['workYear'] = 1;
        }else{
            $indexArr['workYear'] = 0;
        }

        //学历
        $indexArr['degree'] = isset($personInfo['person']['degree'])&& $personInfo['person']['degree'] != 0 ? $personInfo['person']['degree']:1;

        //年龄 从生日中获取
        $personAge = isset($personInfo['person']['birthday']) ? date('Y') - date('Y', strtotime($personInfo['person']['birthday'])) : 999;//未填写当最大处理
        $indexArr['age'] = $personAge >= 41 ? 4 : ($personAge >= 31 ? 3 : ($personAge >= 27 ? 2 : ($personAge >= 24 ? 1:0)));

        //年薪
        $personSalaryMap = Yii::getConfig('personSalary', '@communal/config/data/person.php');
        $personSalaryMin = isset($personInfo['desiredJob'][0]['desired_salary']) ? $personSalaryMap[$personInfo['desiredJob'][0]['desired_salary']]['min'] : 0;
        $personSalaryMax = isset($personInfo['desiredJob'][0]['desired_salary']) ? $personSalaryMap[$personInfo['desiredJob'][0]['desired_salary']]['max'] : 99999999;
        $salary = $personSalaryMax ==99999999?$personSalaryMin:($personSalaryMin+$personSalaryMax)/2;  //取最大薪水和最小薪水的平均值
        //汇率
        $currencyRateMap = Yii::getConfig('currencyRate', '@communal/config/data/person.php');
        if (isset($currencyRateMap[$personInfo['desiredJob'][0]['desired_salary_currency']])) {
            $salary *= $currencyRateMap[$personInfo['desiredJob'][0]['desired_salary_currency']];
        }

        //月薪转年薪
        if(!(isset($personInfo['desiredJob']['desired_salary_mode']) && $personInfo['desiredJob']['desired_salary_mode'] == 2)){
            $salary *= 12;
        }
        if($salary > 300000){
            $indexArr['salary'] = 7;
        }elseif($salary >= 200000 ){
            $indexArr['salary'] = 6;
        }elseif($salary >= 150000){
            $indexArr['salary'] = 5;
        }elseif($salary >= 130000){
            $indexArr['salary'] = 4;
        }elseif($salary >= 90000){
            $indexArr['salary'] = 3;
        }elseif($salary >= 50000){
            $indexArr['salary'] = 2;
        }elseif($salary > 0){
            $indexArr['salary'] = 1;
        }else{
            $indexArr['salary'] = 0;
        }
        return $indexArr;
    }

    private function getCompetitiveRank($userIndexes,$pool){

        $rank = [];

        //去数据池中查找排名
        $rank['workYear'] = $this->getPosition($pool['workYear'],$userIndexes['workYear'],2);
        $rank['degree'] = $this->getPosition($pool['degree'],$userIndexes['degree'],2);
        $rank['age'] = $this->getPosition($pool['age'],$userIndexes['age'],1);
        $rank['salary'] = $this->getPosition($pool['salary'],$userIndexes['salary'],2);
        $rank['average'] = round(array_sum($rank)/4);
        return $rank;
    }

    private function getPosition($arr,$index,$order = 1){
        if($order != 1){
            krsort($arr);
        }
        $num = 0;
        foreach($arr as $key=>$value){
            if($key == $index){
                break;
            }
            $num += $value;
        }
        return $num+1;
    }

    /**
     * 获取工作经历
     * @param string $column
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getWorkHistory($column = '*'){
        return PersonResumeWorkHistory::find()
            ->select($column)
            ->where([
                'p_userid'=>$this->p_userid,
            ])
            ->orderBy('begin_year DESC')
            ->asArray()->all();
    }

    /**
     * 获取语言信息
     *
     * @return array [language => ability]
     */
    public  function getLanguageInfoByUserId()
    {


        $data   = PersonResumeLanguage::find()->select( 'language, ability')->distinct()->where([
            'user_id' => $this->p_userid
        ])->asArray()->all();
        $result = [];
        foreach($data as $val) {
            $result[$val['language']] = $val['ability'];
        }

        return $result;

    }


}
