<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * QuestionModule.php
 */

namespace app\forum\modules\content;

use app\forum\modules\activity\ActivityModule;
use app\forum\modules\title\TitleImagesModule;
use app\forum\modules\title\TitleModule;
use Cross\Core\Helper;

/**
 * 问题相关
 *
 * @Auth: cmz <393418737@qq.com>
 * Class QuestionModule
 * @package modules
 */
class QuestionModule extends ContentModule
{
    const ANSWER_NORMAL = 1; //正常
    const ANSWER_HIDDEN = -1; //折叠
    const ANSWER_BLOCKED = -2; //屏蔽

    /**
     * 问题基本信息
     *
     * @param int $question_id
     * @param int $uid
     * @return mixed
     * @throws \Cross\Exception\CoreException
     */
    function getQuestionInfo($question_id, $uid)
    {
        $uid = (int)$uid;
        $question_id = (int)$question_id;
        $base_info_sql = $this->link->select('t.*, fi.id invite_id, q.question_id, q.question_id content_id, q.best_answer_id, q.question_content, q.hits, q.hits_update_time')
            ->from("{$this->questions} q
                LEFT JOIN {$this->title} t ON q.title_id=t.title_id
                LEFT JOIN {$this->invite} fi ON q.title_id=fi.title_id AND fi.uid={$uid}")
            ->where("question_id={$question_id}")->getSQL(true);

        return $this->getContentBaseExtendInfo($base_info_sql, $uid);
    }

    /**
     * 答案列表
     *
     * @param array $condition
     * @param array $page
     * @return mixed
     */
    function listAnswer($condition = array(), &$page)
    {
        return $this->link->find("{$this->answers} a LEFT JOIN {$this->user} u ON a.uid=u.uid", 'a.*, u.nickname', $condition, '1 DESC', $page);
    }

    /**
     * 更新答案状态
     *
     * @param int $answer_id
     * @param int $status
     * @return bool
     */
    function updateAnswerStatus($answer_id, $status)
    {
        return $this->link->update($this->answers, array(
            'status' => (int)$status
        ), array('answer_id' => (int)$answer_id));
    }

    /**
     * 回答列表
     *
     * @param int $loginUid
     * @param int $question_id
     * @param string $sort
     * @param array $page
     * @param string $status
     * @param bool $use_limit
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function findQuestionAnswer($loginUid, $question_id, $sort, &$page, $status = 'a.status=1', $use_limit = true)
    {
        $list = array();
        $question_id = (int)$question_id;

        //记录总条数
        if ($use_limit) {
            $total = $this->link->get($this->answers, 'count(1) result_count', "question_id = {$question_id} AND status=1");
        } else {
            $total['result_count'] = 0;
        }

        //评论条数
        $comment_count_sql = $this->link->select('count(ac.comment_id)')
            ->from("{$this->answers_comment} ac")
            ->where('aas.answer_id=ac.answer_id AND status=1')->getSQL(true);

        //按时间排序
        if ($sort == 2) {
            $query = $this->link->select("a.*, u.account, u.nickname, u.introduce, u.avatar, aas.up_count, ({$comment_count_sql}) comment_count")
                ->from("{$this->answers} a LEFT JOIN {$this->answers_stat} aas ON a.answer_id=aas.answer_id
                        LEFT JOIN {$this->user} u ON u.uid=a.uid")
                ->where("a.question_id = {$question_id} and {$status}")
                ->orderBy('a.answer_id DESC');
        } else {
            $query = $this->link->select("a.*, u.account, u.nickname, u.introduce, u.avatar, aas.up_count, ({$comment_count_sql}) comment_count")
                ->from("{$this->answers_stat} aas LEFT JOIN {$this->answers} a ON aas.answer_id=a.answer_id
                        LEFT JOIN {$this->user} u ON u.uid=a.uid")
                ->where("aas.question_id = {$question_id} and {$status}")
                ->orderBy('aas.up_count DESC');
        }

        if ($use_limit) {
            $page['result_count'] = $total['result_count'];
            $page['limit'] = max(1, (int)$page['limit']);
            $page['total_page'] = ceil($page['result_count'] / $page['limit']);

            if ($page['p'] <= $page['total_page']) {
                $page['p'] = max(1, $page['p']);
                $start = ($page['p'] - 1) * $page['limit'];
                $query->limit($start, $page['limit']);
            } else {
                return $list;
            }
        }

        $list = $query->stmt()->fetchAll(\PDO::FETCH_ASSOC);
        if (!empty($list)) {
            $standMap = array();
            if ($loginUid > 0) {
                $answer_ids = array();
                array_map(function (&$l) use (&$answer_ids) {
                    $answer_ids[] = $l['answer_id'];
                }, $list);

                $answer_id_string = implode(',', $answer_ids);
                $standMap = $this->link->select('answer_id, stand')->from($this->answers_stand)
                    ->where("uid={$loginUid} AND answer_id IN({$answer_id_string})")
                    ->stmt()->fetchAll(\PDO::FETCH_KEY_PAIR);
            }

            foreach ($list as &$l) {
                if (isset($standMap[$l['answer_id']])) {
                    $l['stand'] = &$standMap[$l['answer_id']];
                } else {
                    $l['stand'] = 0;
                }
            }
        }

        return $list;
    }

    /**
     * 获取被屏蔽的答案数
     *
     * @param int $question_id
     * @return int
     */
    function getBlockAnswerCount($question_id)
    {
        $info = $this->link->get($this->answers, 'count(1) count', array(
            'question_id' => $question_id,
            'status' => array('<>', 1)
        ));

        if ($info) {
            return $info['count'];
        }

        return 0;
    }

    /**
     * 保存答案
     *
     * @param int $uid
     * @param int $question_id
     * @param bool $have_vote_answer 是否已有投票答案
     * @param int $title_id
     * @param string $answer
     * @param null|int $invite_id
     * @return bool|mixed
     * @throws \Cross\Exception\CoreException
     */
    function saveAnswer($uid, $question_id, $have_vote_answer, $title_id, $answer, $invite_id = null)
    {
        $uid = (int)$uid;
        $title_id = (int)$title_id;
        $question_id = (int)$question_id;
        $images_list = array();
        $answer = $this->getContent($answer, $images_list);

        $answer_id = $this->link->add($this->answers, array(
            'uid' => $uid,
            'title_id' => $title_id,
            'question_id' => $question_id,
            'answer_content' => $answer,
            'answer_ip' => Helper::getLongIp(),
            'answer_time' => TIME
        ));

        if ($answer_id) {

            //处理邀请回答
            if ($invite_id) {
                $this->updateInviteStatus($invite_id, self::INVITE_FINISH);
            }

            //初始化统计
            $this->link->add($this->answers_stat, array(
                'answer_id' => $answer_id,
                'question_id' => $question_id,
                'up_count' => 0
            ));

            //保存图片
            $TIM = new TitleImagesModule();
            $TIM->saveImages($title_id, $images_list, TitleImagesModule::LOCATION_INTERACT, $answer_id);

            //如果没有投票
            //当前答案设置为最佳答案
            if (!$have_vote_answer) {
                $this->link->update($this->questions, array('best_answer_id' => $answer_id), array(
                    'question_id' => $question_id
                ));
            }

            //增加互动次数计数
            $TITLE = new TitleModule();
            $TITLE->addInteractCount($title_id);

            //增加动态
            $ACTIVITY = new ActivityModule();
            $ACTIVITY->add($uid, $title_id, ActivityModule::QUESTION_ANSWER, $answer_id);
        }

        return $answer_id;
    }

    /**
     * 添加邀请记录
     *
     * @param int $uid
     * @param $title_id
     * @return bool
     * @throws \Cross\Exception\CoreException
     */
    function inviteUser($uid, $title_id)
    {
        $inviteInfo = $this->link->get($this->invite, '*', array('uid' => $uid, 'title_id' => $title_id));
        if ($inviteInfo) {
            switch ($inviteInfo['status']) {
                case self::INVITE_FINISH:
                    $retCode = 200803;
                    break;

                case self::INVITE_IGNORE:
                    $retCode = 200804;
                    break;

                default:
                    $retCode = 200802;
                    break;
            }
        } else {
            $ret = $this->link->add($this->invite, array(
                'uid' => $uid,
                'title_id' => $title_id,
                'invite_time' => TIME,
            ));
            if ($ret) {
                $retCode = 200802;
            } else {
                $retCode = 200801;
            }
        }

        return $this->result($retCode);
    }

    /**
     * 待参与的邀请
     *
     * @param int $uid
     * @return int
     */
    function getReceivedInviteCount($uid)
    {
        $info = $this->link->get($this->invite, 'count(1) total', array(
            'uid' => $uid,
            'status' => 0
        ));

        return (int)$info['total'];
    }

    /**
     * 邀请我参与的主题
     *
     * @param int $uid
     * @param int $status
     * @param array $page
     * @return array
     */
    function findUserInviteContent($uid, $status = 0, array &$page)
    {
        $uid = (int)$uid;
        $status = (int)$status;
        $total = $this->link->get($this->invite, 'count(1) total_result', array('uid' => $uid, 'status' => $status));
        $page['result_count'] = $total['total_result'];

        $page['limit'] = max(1, (int)$page['limit']);
        $page['total_page'] = ceil($page['result_count'] / $page['limit']);

        $list = array();
        if ($page['p'] <= $page['total_page']) {

            $page['p'] = max(1, $page['p']);
            $start = ($page['p'] - 1) * $page['limit'];

            $title_sql = $this->link->select('id invite_id, title_id, count, status')
                ->from($this->invite)->where("uid={$uid} AND status={$status}")->orderBy('id DESC')
                ->limit($start, $page['limit'])->getSQL(true);

            $TITLE = new TitleModule();
            $list = $TITLE->contentListDetail($title_sql, 'invite_id, count, status');
        }

        return $list;
    }

    /**
     * 设置邀请状态
     *
     * @param int $invite_id
     * @return bool
     */
    function ignoreInvite($invite_id)
    {
        return $this->updateInviteStatus($invite_id, self::INVITE_IGNORE);
    }

    /**
     * 更新统计状态
     *
     * @param int $uid
     * @param int $question_id
     * @param int $answer_id
     * @param string $action
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function updateAnswerVote($uid, $question_id, $answer_id, $action)
    {
        $question_info = $this->link->select('t.up_count, t.topic_ids, q.title_id')
            ->from("{$this->questions} q LEFT JOIN {$this->title} t ON q.title_id=t.title_id")
            ->where(array('q.question_id' => $question_id))
            ->stmt()->fetch(\PDO::FETCH_ASSOC);

        if (!$question_info) {
            return $this->result(200220);
        }

        $title_id = $question_info['title_id'];
        $best_answer = $this->link->select('answer_id, up_count')
            ->from($this->answers_stat)->where(array('question_id' => $question_id))
            ->orderBy('up_count DESC')->limit(1)
            ->stmt()->fetch(\PDO::FETCH_ASSOC);

        $answer_info = $this->link->get($this->answers_stat, 'up_count', array('answer_id' => $answer_id));
        $current_up_count = $answer_info['up_count'];

        //用户立场
        $userStand = $this->link->get($this->answers_stand, 'id, stand', array('uid' => $uid, 'answer_id' => $answer_id));
        $stand = ($action == 'up') ? 1 : 2;

        if ($userStand) {
            //取消赞成或反对
            if ($userStand['stand'] == 1) {
                if ($action == 'up') {
                    $stand = 0;
                    $current_up_count -= 1;
                } else {
                    $stand = 2;
                    $current_up_count -= 2;
                }
                $act_log_action = 'del';
            } elseif ($userStand['stand'] == 2) {
                if ($action == 'up') {
                    $stand = 1;
                    $current_up_count += 2;
                    $act_log_action = 'add';
                } else {
                    $stand = 0;
                    $current_up_count += 1;
                    $act_log_action = 'del';
                }
            } else {
                if ($stand == 1) {
                    $current_up_count++;
                    $act_log_action = 'add';
                } else {
                    $current_up_count--;
                    $act_log_action = 'del';
                }
            }

            $this->link->update($this->answers_stand, array('stand' => $stand), array('id' => $userStand['id']));
        } else {
            if ($stand == 1) {
                $current_up_count++;
                $act_log_action = 'add';
            } else {
                $current_up_count--;
                $act_log_action = 'del';
            }

            $this->link->add($this->answers_stand, array(
                'uid' => $uid,
                'answer_id' => $answer_id,
                'stand' => $stand
            ));
        }

        //是否需要更新最佳答案
        if ($current_up_count > $best_answer['up_count']) {
            $this->link->update($this->questions, array('best_answer_id' => $answer_id), array(
                'question_id' => $question_id
            ));
        }

        //更新点赞统计
        $this->link->update($this->answers_stat, array('up_count' => $current_up_count), array(
            'answer_id' => $answer_id
        ));

        $ACT = new ActivityModule();
        if ($act_log_action == 'del') {
            $ACT->hasDelete($uid, $title_id, ActivityModule::QUESTION_ANSWER_UP, $answer_id);
        } else {
            $ACT->add($uid, $title_id, ActivityModule::QUESTION_ANSWER_UP, $answer_id);
        }

        return $this->result(1, array('stand' => $stand, 'up_count' => $current_up_count));
    }
}
