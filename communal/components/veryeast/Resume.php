<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/30
 * Time: 10:26
 */

namespace communal\components\veryeast;


use communal\components\BaseComponent;
use communal\models\ve_person\PersonResumeCoverLetter;
use yii\base\Exception;

class Resume extends BaseComponent
{

    public function modifyCoverLetter($userId,$letterId,$letterTitle,$letterContent){
        $letter = PersonResumeCoverLetter::findOne(['id' => $letterId]);
        if(!$letter || $letter->delete_type != 0 || $letter->user_id != $userId){
            throw new Exception('编辑失败，无法找到所需要的求职信');
        }
        $letter->title = $letterTitle;
        $letter->content = $letterContent;
        $letter->modify_time = date('c');
        if($letter->save()){
            return true;
        }else{
            throw new Exception('保存失败');
        }

    }
}