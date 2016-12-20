<?php
namespace app\forum\modules\message;

use app\forum\modules\common\BaseModule;
use Cross\Core\Helper;

/**
 * @Auth wonli <wonli@live.com>
 * MessageModule.php
 */
class MessageModule extends BaseModule
{
    /**
     * 发送消息给用户
     *
     * @param int $send_uid
     * @param int $receiver_uid
     * @param string $content
     * @return bool
     */
    function sendMessage($send_uid, $receiver_uid, $content)
    {
        $content = self::getEntitiesData(strip_tags($content));
        if (empty($content)) {
            return $this->result(201010);
        }

        $content_length = Helper::strLen($content);
        if ($content_length > 126) {
            return $this->result(201013);
        }

        if ($send_uid == $receiver_uid) {
            return $this->result(201011);
        }

        $data = array(
            'fields' => array(
                'uid',
                'receiver_uid',
                'sender',
                'receiver',
                'message_type',
                'content',
                'send_time',
                'read_time',
                'status'
            ),
            'values' => array(
                array($send_uid, $receiver_uid, $send_uid, $receiver_uid, 1, $content, TIME, TIME, 1),
                array($receiver_uid, $send_uid, $send_uid, $receiver_uid, 1, $content, TIME, 0, 1),
            ),
        );

        $message_id = $this->link->add($this->message, $data, true);
        if ($message_id) {
            return $this->result(1, array('message_id' => $message_id));
        }

        return $this->result(201012);
    }

    /**
     * 发送系统消息
     *
     * @param int $receiver_uid
     * @param string $content
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function sendSysMessage($receiver_uid, $content)
    {
        $data = array(
            'uid' => $receiver_uid,
            'receiver_uid' => 0,
            'sender' => 0,
            'receiver' => $receiver_uid,
            'message_type' => 2,
            'content' => $content,
            'send_time' => TIME,
            'read_time' => 0,
            'status' => 1
        );

        $message_id = $this->link->add($this->message, $data);
        if ($message_id) {
            return $this->result(1, array('message_id' => $message_id));
        }

        return $this->result(201012);
    }

    /**
     * 获取系统通知
     *
     * @param int $uid
     * @param array $page
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function sysMessage($uid, array &$page)
    {
        return $this->link->find($this->message, '*', array('uid' => (int)$uid, 'message_type' => 2), 'id DESC', $page);
    }

    /**
     * 私信会话列表
     *
     * @param int $uid
     * @param array $page
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function messageDialog($uid, array &$page)
    {
        $uid = (int)$uid;
        $dialogInfo = $this->link->select('count(1) total')->from($this->message)
            ->where(array('uid' => $uid, 'message_type' => 1))->groupBy('receiver_uid')->stmt()->fetchAll(\PDO::FETCH_ASSOC);

        $page['result_count'] = count($dialogInfo);

        $page['limit'] = max(1, (int)$page['limit']);
        $page['total_page'] = ceil($page['result_count'] / $page['limit']);

        $list = array();
        if ($page['p'] <= $page['total_page']) {

            $page['p'] = max(1, $page['p']);
            $start = ($page['p'] - 1) * $page['limit'];

            $dialogSQL = $this->link->select('uid, receiver_uid, MAX(id) AS id, count(id) AS msg_count')
                ->from($this->message)->where("uid={$uid} AND message_type=1")->groupBy('receiver_uid')->limit($start, $page['limit'])
                ->getSQL(true);

            $list = $this->link->select('a.*,
                     m.sender, m.receiver, m.message_type, m.content, m.send_time, m.read_time,
                     u.account, u.nickname, u.introduce, u.avatar')
                ->from("({$dialogSQL}) a LEFT JOIN cpf_message m ON a.id = m.id LEFT JOIN cpf_user u ON m.sender = u.uid")
                ->stmt()->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $list;
    }

    /**
     * 对话消息列表
     *
     * @param int $uid
     * @param int $receiver_uid
     * @param array $page
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function messageList($uid, $receiver_uid, array &$page)
    {
        $condition = array('m.uid = ? AND m.receiver_uid = ?', array($uid, $receiver_uid));
        $msgInfo = $this->link->get("{$this->message} m", 'count(1) total', $condition);
        $page['result_count'] = $msgInfo['total'];

        $page['limit'] = max(1, (int)$page['limit']);
        $page['total_page'] = ceil($page['result_count'] / $page['limit']);

        $list = array();
        if ($page['p'] <= $page['total_page']) {

            $page['p'] = max(1, $page['p']);
            $start = ($page['p'] - 1) * $page['limit'];

            $list = $this->link->select('m.*, u.account, u.nickname, u.introduce, u.avatar')
                ->from("{$this->message} m LEFT JOIN {$this->user} u ON m.sender=u.uid")
                ->where($condition)->orderBy('id DESC')->limit($start, $page['limit'])
                ->stmt()->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $list;
    }

    /**
     * 新消息数量
     *
     * @param int $uid
     * @return mixed
     * @throws \Cross\Exception\CoreException
     */
    function getNewMessageCount($uid)
    {
        $count = $this->link->select('count(1) count')->from($this->message)
            ->where(array('receiver' => $uid, 'read_time' => 0))->limit(1)->stmt()->fetch(\PDO::FETCH_ASSOC);

        return $count;
    }

    /**
     * 更新消息已读时间
     *
     * @param $ids
     */
    function updateMessageReadTime($ids)
    {
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        if (!empty($ids)) {
            $this->link->update($this->message, array('read_time' => TIME), array(
                'id' => array('IN', $ids)
            ));
        }
    }

    /**
     * 删除消息
     *
     * @param int $uid
     * @param int $message_id
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function deleteMessage($uid, $message_id)
    {
        $message_info = $this->link->get($this->message, '*', array('id' => $message_id));
        if (empty($message_info)) {
            return $this->result(201014);
        }

        if ($uid !== $message_info['uid']) {
            return $this->result(201015);
        }

        $ret = $this->link->del($this->message, array('id' => $message_id));
        if ($ret) {
            return $this->result(1);
        }

        return $this->result(201016);
    }

    /**
     * 删除所有对话消息
     *
     * @param int $uid
     * @param int $receiver_uid
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function deleteMessageDialog($uid, $receiver_uid)
    {
        $ret = $this->link->del($this->message, array(
            'uid' => $uid,
            'receiver_uid' => $receiver_uid
        ));

        if ($ret) {
            return $this->result(1);
        }

        return $this->result(201017);
    }
}
