<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Chat.php
 */

namespace app\forum\controllers;

use app\forum\modules\account\AccountModule;
use app\forum\modules\message\MessageModule;

/**
 * 私信
 *
 * @Auth wonli <wonli@live.com>
 * Class Message
 * @package app\forum\controllers
 */
class Message extends WantLogin
{
    /**
     * @cp_params receiver_uid, p
     */
    function with()
    {
        $receiver_uid = (int)$this->params['receiver_uid'];

        $AM = new AccountModule();
        $receiver_user_info = $AM->getAccountInfoByUid($receiver_uid);
        if (!$receiver_user_info) {
            $this->data['status'] = 200401;
            $this->alertMessage($this->data);
        }

        $MSG = new MessageModule();
        $content = $this->postData('content');
        if ($this->is_post()) {
            if (empty($content)) {
                $this->alertMessage(201010, 'warning');
            } else {
                $ret = $MSG->sendMessage($this->uid, $receiver_uid, $content);
                if ($ret['status'] != 1) {
                    $this->alertMessage($ret['status'], 'warning');
                } else {
                    $this->to('message:with', array('receiver_uid' => $receiver_uid));
                }
            }
        }

        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 10,
            'half' => 5,
            'link' => array('message:with', array('receiver_uid' => $receiver_uid))
        );

        $message_list = $MSG->messageList($this->uid, $receiver_uid, $page);

        //设置消息状态已读
        $unread_ids = array();
        foreach($message_list as $m) {
            if($m['read_time'] == 0) {
                $unread_ids[] = $m['id'];
            }
        }

        $MSG->updateMessageReadTime($unread_ids);
        $this->data['page'] = $page;
        $this->data['message_list'] = $message_list;

        if($this->is_ajax_request()) {
            $this->view->dialogMessageList($message_list);
        } else {
            $this->display($this->data);
        }
    }

}
