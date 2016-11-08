<?php

namespace communal\components\veryeast;

use Yii;
use communal\components\BaseComponent;
use communal\models\ve_main\MessagePerson;
use communal\models\ve_main\MessagePersonRelation;
use communal\models\ve_person\PersonInfo;

/**
 * Message component
 */
class Message extends BaseComponent
{
    /**
     * 发送个人消息
     *
     * @param  int $fromUserid
     * @param  int $toUserid
     * @param  string $title
     * @param  string $content
     * @param  int $type
     * @param  array  $meta
     * @return FALSE/int
     */
    public function sendPersonMessage($fromUserid, $toUserid, $title, $content, $type, array $meta = array())
    {
        $MessagePerson         = new MessagePerson();
        $MessagePersonRelation = new MessagePersonRelation();
        $transaction           = $MessagePerson->getDb()->beginTransaction();

        try {

            $MessagePerson->title       = $title;
            $MessagePerson->content     = $content;
            $MessagePerson->type        = $type;
            $MessagePerson->meta        = json_encode($meta);
            $MessagePerson->to_userid   = $toUserid;
            $MessagePerson->from_userid = $fromUserid;
            
            if(!$MessagePerson->save()) {
                $transaction->rollback();
                return FALSE;
            }

            $messageId = $MessagePerson->id;

            $MessagePersonRelation->user_id    = $toUserid;
            $MessagePersonRelation->message_id = $messageId;

            if(!$MessagePersonRelation->save()) {
                $transaction->rollback();
                return FALSE;
            };

            $transaction->commit();

            PersonInfo::findOne(['user_id' => $toUserid])->updateCounters( ['message_unread_counts' => 1] );

            //@todo 加入任务队列
            /*\common\apis\resource\Task::add('Push.veryeastPersonMessage', array(
                'p_userId' => $toUserid,
                'c_userId' => $fromUserid,
                'notice_type' => 1,
                'msg_id' => $messageId,
                'msg_type' => $type == 2 ? 1 : 2, 
            ));*/

            return (int)$messageId;

        } catch (Exception $e) {

            $transaction->rollback();
            return FALSE;
        }
    }
}
