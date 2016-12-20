<?php

namespace app\forum\modules\following;

use app\forum\modules\activity\ActivityModule;
use app\forum\modules\common\BaseModule;
use app\forum\modules\title\TitleModule;

/**
 * @Auth: cmz <393418737@qq.com>
 * FollowingModule.php
 */
class FollowingModule extends BaseModule
{
    const USER_FOLLOW = 1;
    const SYSTEM_FOLLOW = 2;

    /**
     * 关注用户
     *
     * @param int $uid
     * @param int $following_uid
     * @param bool $get_statistics
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function user($uid, $following_uid, $get_statistics = false)
    {
        $uid = (int)$uid;
        $following_uid = (int)$following_uid;

        //对自己进行操作时无效
        if ($uid == $following_uid) {
            return $this->result(1);
        }

        $following = $this->link->get($this->following_user, 'id', array('uid' => $uid, 'following_uid' => $following_uid));
        if ($following) {
            $act = 'unFollow';
            $this->link->del($this->following_user, array('id' => $following['id']));
        } else {
            $act = 'follow';
            $following_user = $this->link->get($this->user, 1, array('uid' => $following_uid));
            if ($following_user) {
                $this->link->add($this->following_user, array(
                    'uid' => $uid,
                    'following_uid' => $following_uid,
                    'following_type' => self::USER_FOLLOW,
                    'following_time' => TIME,
                ));
            } else {
                return $this->result(200501);
            }
        }

        $data['act'] = $act;
        if ($get_statistics) {
            $data['statistics'] = $this->getFollowStatistics($following_uid);
        }

        return $this->result(1, $data);
    }

    /**
     * 批量关注用户
     *
     * @param int $uid
     * @param array $users_id
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function multiFollowUser($uid, array $users_id)
    {
        $uid = (int)$uid;
        $target_user_list = $this->link->getAll($this->user, 'uid', array('uid' => array('IN', $users_id)));
        $user_following = $this->link->getAll($this->following_user, 'following_uid', array('uid' => $uid));
        $followed_user = array();
        foreach ($user_following as $f) {
            $followed_user[$f['following_uid']] = true;
        }

        $data['fields'] = array('uid', 'following_uid', 'following_type', 'following_time');
        foreach ($target_user_list as $u) {
            if (!isset($followed_user[$u['uid']])) {
                $data['values'][] = array($uid, $u['uid'], self::USER_FOLLOW, TIME);
            }
        }

        if (!empty($data['values'])) {
            $ret = $this->link->add($this->following_user, $data, true);
            if ($ret) {
                return $this->result(1);
            }

            return $this->result(200523);
        }

        return $this->result(1);
    }

    /**
     * 是否关注某人
     *
     * @param int $uid
     * @param int $target_uid
     * @return bool
     */
    function isFollowUser($uid, $target_uid)
    {
        return (bool)$this->link->get($this->following_user, '1', array(
            'uid' => (int)$uid, 'following_uid' => (int)$target_uid
        ));
    }

    /**
     * 关注统计
     *
     * @param int $uid
     * @return mixed
     */
    function getFollowStatistics($uid)
    {
        $uid = (int)$uid;
        $follow_me_count = $this->link->select('count(1)')->from($this->following_user)
            ->where("following_uid={$uid} AND uid <> {$uid}")->getSQL(true);
        $me_follow_count = $this->link->select('count(1)')->from($this->following_user)
            ->where("uid={$uid} AND following_uid <> {$uid}")->getSQL(true);

        return $this->link->select("({$follow_me_count}) follow_me_count, ({$me_follow_count}) me_follow_count")
            ->stmt()->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * 关注话题
     *
     * @param int $uid
     * @param int $topic_id
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function topic($uid, $topic_id)
    {
        $topic_id = (int)$topic_id;
        $following = $this->link->get($this->following_topic, 1, array('uid' => $uid, 'topic_id' => $topic_id));
        if ($following) {
            return $this->result(1);
        }

        $topic_info = $this->link->get($this->topic, 1, array('topic_id' => $topic_id));
        if ($topic_info) {
            $this->link->add($this->following_topic, array(
                'uid' => $uid,
                'topic_id' => $topic_id,
                'following_time' => TIME
            ));

            return $this->result(1);
        }

        return $this->result(200510);
    }

    /**
     * 关注/取消关注内容
     *
     * @param int $uid
     * @param int $title_id
     * @param int $content_type
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function content($uid, $title_id, $content_type)
    {
        $title_id = (int)$title_id;
        $ACT = new ActivityModule();
        $following = $this->link->get($this->following_content, 'id', array('uid' => $uid, 'title_id' => $title_id));
        if ($following) {
            $del = $this->link->del($this->following_content, array('id' => $following['id']));
            if ($del) {
                $act_info = $this->link->get($this->following_act, 'id, act_id', array('follow_id' => $following['id']));
                //删除动态及对应关系数据
                if (!empty($act_info)) {
                    $this->link->del($this->user_act_log, array('id' => $act_info['act_id']));
                    $this->link->del($this->following_act, array('id' => $act_info['id']));
                }

                return $this->result(1, array('act' => 0, 'count' => $this->getContentTotalFollow($title_id)));
            } else {
                return $this->result(200521);
            }
        }

        $content_type_act_map = array(
            BaseModule::TYPE_POSTS => ActivityModule::POSTS_FOLLOW,
            BaseModule::TYPE_ARTICLE => ActivityModule::ARTICLE_FOLLOW,
            BaseModule::TYPE_QUESTION => ActivityModule::QUESTION_FOLLOW,
        );

        if (!isset($content_type_act_map[$content_type])) {
            return $this->result(200503);
        }

        $action_type = $content_type_act_map[$content_type];
        $content_info = $this->link->get($this->title, 1, array('title_id' => $title_id));
        if ($content_info) {

            $follow_id = $this->link->add($this->following_content, array(
                'uid' => $uid,
                'title_id' => $title_id,
                'following_time' => TIME,
                'last_view_time' => TIME,
            ));

            if ($follow_id) {
                //添加动态
                $act_id = $ACT->add($uid, $title_id, $action_type, $follow_id);

                //保存动态id
                $this->link->add($this->following_act, array(
                    'act_id' => $act_id,
                    'follow_id' => $follow_id
                ));

                return $this->result(1, array('act' => 1, 'count' => $this->getContentTotalFollow($title_id)));
            } else {
                return $this->result(200522);
            }
        }

        return $this->result(200520);
    }

    /**
     * 获取关注数量
     *
     * @param int $title_id
     * @return int
     */
    function getContentTotalFollow($title_id)
    {
        $total_following = $this->link->get($this->following_content, 'count(1) count', array('title_id' => $title_id));
        return (int)$total_following['count'];
    }

    /**
     * 取消关注话题
     *
     * @param int $uid
     * @param int $topic_id
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function unFollowingTopic($uid, $topic_id)
    {
        $uid = (int)$uid;
        $topic_id = (int)$topic_id;
        $this->link->del($this->following_topic, array('uid' => $uid, 'topic_id' => $topic_id));
        return $this->result(1);
    }

    /**
     * 关注的内容是否有新内容
     *
     * @param int $uid
     * @return bool
     * @throws \Cross\Exception\CoreException
     */
    function followContentNewTips($uid)
    {
        $dataList = $this->link->select('t.last_interact_time, fc.last_view_time')
            ->from("{$this->following_content} fc LEFT JOIN {$this->title} t ON fc.title_id=t.title_id")
            ->where(array('fc.uid' => (int)$uid))->stmt()->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($dataList)) {
            return false;
        }

        foreach ($dataList as $data) {
            if ($data['last_interact_time'] >= $data['last_view_time']) {
                return true;
            }
        }

        return false;
    }

    /**
     * 更新关注内容最后查看时间
     *
     * @param string $follow_id
     */
    function updateLastViewTime($follow_id)
    {
        $this->link->update($this->following_content, array('last_view_time' => TIME), array('id' => (int)$follow_id));
    }

    /**
     * 用户关注的内容
     *
     * @param int $uid
     * @param array $page
     * @param array $title_ids
     * @return array
     */
    function findUserFollowContent($uid, array &$page, &$title_ids = array())
    {
        $uid = (int)$uid;
        $total = $this->link->get($this->following_content, 'count(1) total_result', array('uid' => $uid));
        $page['result_count'] = $total['total_result'];

        $page['limit'] = max(1, (int)$page['limit']);
        $page['total_page'] = ceil($page['result_count'] / $page['limit']);

        $list = array();
        if ($page['p'] <= $page['total_page']) {

            $page['p'] = max(1, $page['p']);
            $start = ($page['p'] - 1) * $page['limit'];

            $title_sql = $this->link->select('id following_id, last_view_time, title_id')
                ->from($this->following_content)->where("uid={$uid}")->orderBy('id DESC')
                ->limit($start, $page['limit'])->getSQL(true);

            $TITLE = new TitleModule();
            $list = $TITLE->contentListDetail($title_sql, 'following_id, last_view_time', $title_ids);
        }

        return $list;
    }

    /**
     * 获取关注话题的人
     *
     * @param string $topic_ids
     * @param int $limit
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function getFollowingTopicUser($topic_ids, $limit = 5)
    {
        $topic = parent::getInputSeparateID($topic_ids);
        $ft = $this->link->select('uid')
            ->from("{$this->following_topic}")
            ->where("topic_id IN ({$topic})")
            ->limit($limit)->getSQL(true);

        return $this->link->select('u.uid, u.account, u.nickname, u.introduce, u.avatar')
            ->from("({$ft}) ft LEFT JOIN {$this->user} u ON ft.uid=u.uid")
            ->stmt()->fetchAll(\PDO::FETCH_ASSOC);

    }

    /**
     * 按话题回答数随机获取推荐用户
     *
     * @param string $topic_ids
     * @param int $limit
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function getTopicAnswerUser($topic_ids, $limit = 5)
    {
        //该话题列表下最新主题
        $topic_ids = parent::getInputSeparateID($topic_ids);
        $newAboutTitleSQL = $this->link->select('topic_id, title_id')
            ->from($this->topic_title_id)->where("topic_id IN({$topic_ids}) AND type=1")
            ->orderBy('id DESC')->limit(1000)->getSQL(true);

        $query = $this->link->select('DISTINCT a.uid, u.nickname, u.introduce, u.avatar, u.account, tti.topic_id, t.topic_name, t.topic_url')
            ->from("({$newAboutTitleSQL}) tti
                LEFT JOIN {$this->answers} a ON a.title_id=tti.title_id
                LEFT JOIN {$this->topic} t ON tti.topic_id=t.topic_id
                LEFT JOIN {$this->user} u ON a.uid=u.uid")
            ->where('a.answer_id>0')->orderBy('RAND()')->limit($limit);

        $data = $query->stmt()->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($data)) {
            return array();
        }

        //话题下的用户
        $answer_user = $answer_topic_count = array();
        foreach ($data as $d) {
            $answer_topic_count[$d['uid']] = 0;
            $answer_user[$d['topic_id']][] = $d['uid'];
        }

        //获取该话题下用户有多少个回答
        foreach ($answer_user as $topic_id => $user) {
            $topicSQL = $this->link->select('title_id')
                ->from($this->topic_title_id)->where("topic_id={$topic_id}")->getSQL(true);

            $condition = sprintf("uid IN(%s) AND title_id IN(%s)", implode(',', $user), $topicSQL);
            $query = $this->link->select('uid, count(1) count')
                ->from($this->answers)->where($condition)->groupBy('uid');

            $query->stmt()->fetchAll(\PDO::FETCH_FUNC, function ($uid, $count) use (&$answer_topic_count) {
                $answer_topic_count[$uid] = $count;
            });
        }

        foreach ($data as &$d) {
            $d['answer_count'] = $answer_topic_count[$d['uid']];
        }

        return $data;
    }

    /**
     * 获取话题关注人数
     *
     * @param int $topic_id
     * @return int
     */
    function getTopicFollowingCount($topic_id)
    {
        $info = $this->link->get($this->following_topic, 'count(1) count', array('topic_id' => $topic_id));
        if ($info) {
            return $info['count'];
        }

        return 0;
    }
}
