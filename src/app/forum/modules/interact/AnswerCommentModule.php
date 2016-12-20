<?php
namespace app\forum\modules\interact;

use app\forum\modules\common\BaseModule;
use Cross\Core\Helper;

/**
 * 答案相关
 *
 * @Auth: cmz <393418737@qq.com>
 * AnswerModule.php
 */
class AnswerCommentModule extends BaseModule
{
    /**
     * 获取答案内容
     *
     * @param int $answer_id
     * @param string $fields
     * @return mixed
     */
    function getAnswerInfo($answer_id, $fields = 'a.*, aas.up_count, u.uid, u.account, u.nickname, u.introduce, u.avatar')
    {
        return $this->link->get("{$this->answers} a
            LEFT JOIN {$this->answers_stat} aas ON a.answer_id=aas.answer_id
            LEFT JOIN {$this->user} u ON a.uid=u.uid", $fields, array(
            'a.answer_id' => (int)$answer_id
        ));
    }

    /**
     * 获取用户对于答案的立场
     *
     * @param int $uid
     * @param int $answer_id
     * @return int
     */
    function getUserStand($uid, $answer_id)
    {
        $stand = $this->link->get($this->answers_stand, '*', array('uid' => (int)$uid, 'answer_id' => (int)$answer_id));
        if ($stand) {
            return $stand['stand'];
        }

        return 0;
    }

    /**
     * 保存答案评论
     *
     * @param int $uid
     * @param int $answer_id
     * @param string $content
     * @return bool|mixed
     * @throws \Cross\Exception\CoreException
     */
    function saveAnswerComment($uid, $answer_id, $content)
    {
        $uid = (int)$uid;
        $answer_id = (int)$answer_id;
        $content = self::getEntitiesData(strip_tags($content));

        if (empty($content)) {
            return $this->result(200910);
        }

        $contentLength = Helper::strLen($content);
        if ($contentLength < 2 || $contentLength > 126) {
            return $this->result(200911);
        }

        $ret = $this->link->add($this->answers_comment, array(
            'uid' => $uid,
            'answer_id' => $answer_id,
            'comment_content' => $content,
            'comment_time' => TIME,
        ));

        if ($ret) {
            return $this->result(1);
        }

        return $this->result(200909);
    }

    /**
     * 获取问题评论
     *
     * @param int $answer_id
     * @param array $page
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function findAnswerComment($answer_id, array &$page)
    {
        $comment_info = $this->link->get($this->answers_comment, 'count(1) total', array('answer_id' => $answer_id));
        $page['result_count'] = $comment_info['total'];

        $page['limit'] = max(1, (int)$page['limit']);
        $page['total_page'] = ceil($page['result_count'] / $page['limit']);

        $list = array();
        if ($page['p'] <= $page['total_page']) {

            $page['p'] = max(1, $page['p']);
            $start = ($page['p'] - 1) * $page['limit'];

            $list = $this->link->select('ac.*, u.account, u.nickname, u.avatar, u.introduce')
                ->from("{$this->answers_comment} ac LEFT JOIN {$this->user} u ON ac.uid=u.uid")
                ->where(array('answer_id' => $answer_id))
                ->orderBy('comment_id DESC')
                ->limit($start, $page['limit'])
                ->stmt()->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $list;
    }
}
