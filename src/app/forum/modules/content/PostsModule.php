<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * PostsModule.php
 */

namespace app\forum\modules\content;

use app\forum\modules\activity\ActivityModule;
use app\forum\modules\title\TitleImagesModule;
use app\forum\modules\title\TitleModule;
use Cross\Core\Helper;

/**
 * 帖子相关
 *
 * @Auth: cmz <393418737@qq.com>
 * Class PostsModule
 * @package modules\content
 */
class PostsModule extends ContentModule
{
    const REPLY_NORMAL = 1; //正常
    const REPLY_HIDDEN = -1; //被折叠
    const REPLY_BLOCKED = -2; //被屏蔽

    /**
     * 帖子信息
     *
     * @param int $posts_id
     * @param int $p
     * @param int $uid
     * @return mixed
     */
    function getPostsInfo($posts_id, $p, $uid)
    {
        $posts_id = (int)$posts_id;
        $base_sql = $this->link->select('posts_id, title_id, hits, hits_update_time')
            ->from($this->posts)->where("posts_id={$posts_id}")->getSQL();

        $posts_info_sql = $this->link->select('t.*, fi.id invite_id, p.posts_id, p.hits, p.hits_update_time,
                p.posts_id content_id, pc.content, pc.p, count(pcc.id) content_page')
            ->from("({$base_sql['sql']}) p
            LEFT JOIN {$this->posts_content} pc ON p.posts_id=pc.posts_id and pc.p={$p}
            LEFT JOIN {$this->posts_content} pcc ON p.posts_id=pcc.posts_id
            LEFT JOIN {$this->invite} fi ON p.title_id=fi.title_id AND fi.uid={$uid}
            LEFT JOIN {$this->title} t ON t.title_id=p.title_id")
            ->getSQL(true);

        return $this->getContentBaseExtendInfo($posts_info_sql, $uid);
    }

    /**
     * 输出帖子指定页码的内容信息
     *
     * @param int $posts_id
     * @param int $p
     * @return mixed
     */
    function getPostsContent($posts_id, $p)
    {
        return $this->link->get($this->posts_content, '*', array('posts_id' => (int)$posts_id, 'p' => (int)$p));
    }

    /**
     * 帖子回复列表
     *
     * @param array $condition
     * @param array $page
     * @return mixed
     */
    function listReply($condition = array(), &$page)
    {
        return $this->link->find("{$this->reply} r LEFT JOIN {$this->user} u ON r.uid=u.uid LEFT JOIN {$this->posts} p ON r.posts_id=p.posts_id",
            'r.*, u.nickname, p.title_id', $condition, '1 DESC', $page);
    }

    /**
     * 更新回复状态
     *
     * @param int $reply_id
     * @param int $status
     * @return bool
     */
    function updateReplyStatus($reply_id, $status)
    {
        return $this->link->update($this->reply, array(
            'status' => (int)$status
        ), array('reply_id' => (int)$reply_id));
    }

    /**
     * 获取已屏蔽的回复数
     *
     * @param int $posts_id
     * @return int
     */
    function getBlockReplyCount($posts_id)
    {
        $info = $this->link->get($this->reply, 'count(1) count', array('posts_id' => (int)$posts_id, 'status' => array('<>', 1)));
        if ($info) {
            return $info['count'];
        }

        return 0;
    }

    /**
     * 获取回复信息
     *
     * @param int $reply_id
     * @param int $uid
     * @return mixed
     */
    function getReplyInfo($reply_id, $uid = 0)
    {
        if ($uid > 0) {
            $reply_info = $this->link->get("{$this->reply} r
                LEFT JOIN {$this->user} u ON r.uid = u.uid
                LEFT JOIN {$this->reply_up} ru ON r.reply_id=ru.reply_id AND ru.uid={$uid}",
                'r.*, ru.id up_id, u.account, u.nickname, u.avatar, u.introduce', array(
                    'r.reply_id' => (int)$reply_id
                ));
            if ($reply_info['up_id']) {
                $reply_info['is_up'] = 1;
            } else {
                $reply_info['is_up'] = 0;
                $reply_info['up_id'] = 0;
            }
        } else {
            $reply_info = $this->link->get("{$this->reply} r
                LEFT JOIN {$this->user} u ON r.uid = u.uid",
                'r.*, u.account, u.nickname, u.avatar, u.introduce', array(
                    'r.reply_id' => (int)$reply_id
                ));
            $reply_info['is_up'] = 0;
            $reply_info['up_id'] = 0;
        }

        return $reply_info;
    }

    /**
     * 获取回复列表
     *
     * @param int $login_uid
     * @param int $posts_id
     * @param string $order
     * @param array $page
     * @param int $status
     * @param bool $use_limit
     * @return mixed
     * @throws \Cross\Exception\CoreException
     */
    function findReply($login_uid, $posts_id, $order, &$page, $status = 1, $use_limit = true)
    {
        $posts_id = (int)$posts_id;
        $order2fields = array(1 => 'r.up_count DESC', 2 => 'r.reply_id DESC');
        if (isset($order2fields[$order])) {
            $order = $order2fields[$order];
        } else {
            $order = $order2fields[1];
        }

        $fields = "r.*, u.account, u.nickname, u.avatar, u.introduce, (select count(1) from {$this->reply_comment} where reply_id=r.reply_id) comment_count";
        if ($use_limit) {
            $reply = $this->link->find("{$this->reply} r
                LEFT JOIN {$this->user} u ON r.uid = u.uid
                LEFT JOIN {$this->reply_comment} ru ON r.reply_id=ru.reply_id",
                $fields, array('r.posts_id' => $posts_id, 'r.status' => $status), $order, $page);
        } else {
            $reply = $this->link->getAll("{$this->reply} r
                LEFT JOIN {$this->user} u ON r.uid = u.uid
                LEFT JOIN {$this->reply_comment} ru ON r.reply_id=ru.reply_id",
                $fields, array('r.posts_id' => $posts_id, 'r.status' => $status), $order);
        }

        $reply_ids = $user_reply_up = array();
        if (!empty($reply) && $login_uid > 0) {
            array_map(function ($r) use (&$reply_ids) {
                $reply_ids[] = $r['reply_id'];
            }, $reply);

            $this->link->select('reply_id')
                ->from($this->reply_up)
                ->where(array('uid' => $login_uid, 'reply_id' => array('IN', $reply_ids)))
                ->stmt()->fetchAll(\PDO::FETCH_FUNC, function ($reply_id) use (&$user_reply_up) {
                    $user_reply_up[$reply_id] = true;
                });
        }

        foreach ($reply as &$r) {
            if (isset($user_reply_up[$r['reply_id']])) {
                $r['is_up'] = 1;
            } else {
                $r['is_up'] = 0;
            }
        }

        return $reply;
    }

    /**
     * 回复的评论列表
     *
     * @param int $reply_id
     * @param array $page
     * @return mixed
     */
    function findReplyCommend($reply_id, &$page = array('p' => 1, 'limit' => 20))
    {
        return $this->link->find("{$this->reply_comment} rc LEFT JOIN {$this->user} u ON rc.uid=u.uid",
            'rc.*,  u.account, u.nickname, u.avatar, u.introduce', array('rc.reply_id' => $reply_id), 'rc.comment_id DESC', $page);
    }

    /**
     * 回复帖子
     *
     * @param int $uid
     * @param int $posts_id
     * @param int $title_id
     * @param string $reply
     * @param int $invite_id
     * @return bool|mixed
     * @throws \Cross\Exception\CoreException
     */
    function saveReply($uid, $posts_id, $title_id, $reply, $invite_id = 0)
    {
        $uid = (int)$uid;
        $title_id = (int)$title_id;
        $posts_id = (int)$posts_id;
        $images_list = array();
        $reply = $this->getContent($reply, $images_list);

        $reply_id = $this->link->add($this->reply, array(
            'uid' => $uid,
            'posts_id' => $posts_id,
            'reply_content' => $reply,
            'reply_ip' => Helper::getLongIp(),
            'reply_time' => TIME
        ));

        if ($reply_id) {

            //处理邀请回答
            if ($invite_id) {
                $this->updateInviteStatus($invite_id, self::INVITE_FINISH);
            }

            //增加互动次数计数
            $TITLE = new TitleModule();
            $TITLE->addInteractCount($title_id);

            //保存图片
            $TIM = new TitleImagesModule();
            $TIM->saveImages($title_id, $images_list, TitleImagesModule::LOCATION_INTERACT, $reply_id);

            //增加动态
            $ACTIVITY = new ActivityModule();
            $ACTIVITY->add($uid, $title_id, ActivityModule::POSTS_REPLY, $reply_id);
        }

        return $reply_id;
    }

    /**
     * 保存回复的评论
     *
     * @param $uid
     * @param $reply_id
     * @param $content
     * @param int $at_reply_id
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function saveReplyComment($uid, $reply_id, $content, $at_reply_id = 0)
    {
        $data = array(
            'uid' => (int)$uid,
            'reply_id' => (int)$reply_id,
            'comment_content' => $content,
            'at_reply_id' => (int)$at_reply_id,
            'comment_time' => TIME,
            'status' => 1,
        );

        $comment_id = $this->link->add($this->reply_comment, $data);
        if ($comment_id) {
            return $this->result(1);
        }

        return $this->result(200240);
    }

    /**
     * 回复操作
     *
     * @param int $uid
     * @param int $reply_id
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function replyUp($uid, $reply_id)
    {
        $is_up = $this->link->get($this->reply_up, 'id', array('uid' => $uid, 'reply_id' => $reply_id));
        $up_count_info = $this->link->get($this->reply, 'up_count', array('reply_id' => $reply_id));
        $up_count = $up_count_info['up_count'];
        if ($is_up) {
            //统计数量-1
            $act_type = 0;
            $up_count = max(0, $up_count - 1);
            $this->link->del($this->reply_up, array('id' => $is_up['id']));
        } else {
            $act_type = 1;
            $up_count += 1;
            $this->link->add($this->reply_up, array('uid' => $uid, 'reply_id' => $reply_id, 'ct' => TIME));
        }

        $this->link->update($this->reply, "up_count={$up_count}", array('reply_id' => $reply_id));
        return $this->result(1, array(
            'act_type' => $act_type,
            'up_count' => $up_count,
        ));
    }

    /**
     * 获取内容最大页数
     *
     * @param int $posts_id
     * @return mixed
     */
    function getPostsContentMaxPage($posts_id)
    {
        $maxInfo = $this->link->get($this->posts_content, 'MAX(p) p', array('posts_id' => $posts_id));
        return max(1, (int)$maxInfo['p']);
    }
}
