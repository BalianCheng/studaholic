<?php
namespace app\forum\modules\activity;

use Cross\Core\Helper;
use app\forum\modules\common\BaseModule;

/**
 * @Auth: cmz <393418737@qq.com>
 * ActivityModule.php
 */
class ActivityModule extends BaseModule
{
    const POSTS = 10; //发帖
    const POSTS_FOLLOW = 11; //关注
    const POSTS_REPLY = 12; //回复

    const QUESTION = 20; //提问
    const QUESTION_FOLLOW = 21;//关注
    const QUESTION_ANSWER = 22; //回答
    const QUESTION_ANSWER_UP = 23; //赞同

    const ARTICLE = 30; //发表
    const ARTICLE_UP = 31; //点赞
    const ARTICLE_FOLLOW = 32; //关注
    const ARTICLE_COMMENT = 33; //评论

    /**
     * 首页动态
     *
     * @param int $uid
     * @param array $page
     * @return mixed
     * @throws \Cross\Exception\CoreException
     */
    function getActivity($uid, &$page = array())
    {
        $uid = (int)$uid;
        $total_info = $this->link->get("{$this->following_user} fu
            INNER JOIN {$this->user_act_log} ual ON fu.following_uid=ual.uid",
            'count(ual.id) total', array('fu.uid' => $uid));

        $page['result_count'] = $total_info['total'];
        $page['limit'] = max(1, (int)$page['limit']);
        $page['total_page'] = ceil($page['result_count'] / $page['limit']);

        $page['p'] = max(1, $page['p']);
        $start = ($page['p'] - 1) * $page['limit'];

        $act_log_sql = $this->link->select('ual.*, t.title, t.uid content_uid,
                t.up_count, t.interact_count, t.post_time, t.status')
            ->from("{$this->user_act_log} ual, {$this->following_user} fu, {$this->title} t")
            ->where("ual.uid=fu.following_uid AND t.status=1 AND t.title_id=ual.title_id AND fu.uid={$uid}")
            ->orderBy('ual.id DESC')->limit($start, $page['limit'])->getSQL(true);

        $act_query = $this->link->select('a.*, u.account, u.nickname, u.avatar,
                uu.account content_account, uu.nickname content_nickname, uu.avatar content_avatar')
            ->from("({$act_log_sql}) a LEFT JOIN {$this->user} u ON a.uid=u.uid
                LEFT JOIN {$this->user} uu ON a.content_uid = uu.uid");

        //获取回复和答案的附加内容
        $act = $act_query->stmt()->fetchAll(\PDO::FETCH_ASSOC);
        $title_ids = $posts_ids = $article_ids = $reply_ids = $answer_ids = $article_comment_ids = array();
        if (!empty($act)) {
            array_map(function ($a) use (&$title_ids, &$posts_ids, &$reply_ids, &$answer_ids, &$article_ids, &$article_comment_ids) {
                $title_ids[$a['title_id']] = $a['title_id'];
                $relation_id = $a['relation_id'];
                switch ($a['action_type']) {
                    case ActivityModule::POSTS:
                        $posts_ids[] = $relation_id;
                        break;

                    case ActivityModule::POSTS_REPLY:
                        $reply_ids[] = $relation_id;
                        break;

                    case ActivityModule::ARTICLE:
                    case ActivityModule::ARTICLE_UP:
                    case ActivityModule::ARTICLE_FOLLOW:
                        $article_ids[] = $relation_id;
                        break;

                    case ActivityModule::ARTICLE_COMMENT:
                        $article_comment_ids[] = $relation_id;
                        break;

                    case ActivityModule::QUESTION_ANSWER:
                    case ActivityModule::QUESTION_ANSWER_UP:
                        $answer_ids[] = $relation_id;
                        break;
                }
            }, $act);
        }

        //主题包含的图片
        $title_images = $this->getTitlesImages($title_ids);

        //帖子内容
        $posts_relations = array();
        if (!empty($posts_ids)) {
            $this->link->select('p.posts_id, LEFT(pc.content, 128) content')
                ->from("{$this->posts} p LEFT JOIN {$this->posts_content} pc ON p.posts_id=pc.posts_id")
                ->where(array('p.posts_id' => array('IN', $posts_ids), 'pc.p' => 1))
                ->stmt()->fetchAll(\PDO::FETCH_FUNC, function ($posts_id, $content) use (&$posts_relations) {
                    $posts_relations[$posts_id] = array(
                        'posts_id' => $posts_id,
                        'content' => preg_replace("~\x{00a0}~siu", '', strip_tags($content))
                    );
                });
        }

        //问题回答内容
        $answer_relations = array();
        if (!empty($answer_ids)) {
            $this->link->select('a.answer_id, a.question_id, LEFT(a.answer_content, 128) answer_content, aas.up_count,
                    u.nickname, u.account, u.introduce')
                ->from("{$this->answers} a LEFT JOIN {$this->user} u ON a.uid=u.uid
                    LEFT JOIN {$this->answers_stat} aas ON a.answer_id=aas.answer_id")
                ->where(array('a.answer_id' => array('IN', $answer_ids)))
                ->stmt()->fetchAll(\PDO::FETCH_FUNC, function ($answer_id, $question_id, $answer_content, $up_count, $nickname, $account, $introduce)
                use (&$answer_relations) {
                    $answer_relations[$answer_id] = array(
                        'answer_id' => $answer_id,
                        'question_id' => $question_id,
                        'nickname' => $nickname,
                        'account' => $account,
                        'introduce' => $introduce,
                        'up_count' => $up_count,
                        'content' => preg_replace("~\x{00a0}~siu", '', strip_tags($answer_content))
                    );
                });
        }

        //帖子回复内容
        $reply_relations = array();
        if (!empty($reply_ids)) {
            $this->link->select('posts_id, reply_id, LEFT(reply_content, 128) reply_content')
                ->from($this->reply)
                ->where(array('reply_id' => array('IN', $reply_ids)))
                ->stmt()->fetchAll(\PDO::FETCH_FUNC, function ($posts_id, $reply_id, $reply_content) use (&$reply_relations) {
                    $reply_relations[$reply_id] = array(
                        'posts_id' => $posts_id,
                        'reply_id' => $reply_id,
                        'content' => preg_replace("~\x{00a0}~siu", '', strip_tags($reply_content))
                    );
                });
        }

        //文章摘要
        $article_relations = array();
        if (!empty($article_ids)) {
            $this->link->select('article_id, summary')
                ->from($this->articles)
                ->where(array('article_id' => array('IN', $article_ids)))
                ->stmt()->fetchAll(\PDO::FETCH_FUNC, function ($article_id, $summary) use (&$article_relations) {
                    $article_relations[$article_id] = array(
                        'article_id' => $article_id,
                        'summary' => $summary
                    );
                });
        }

        //文章评论
        $article_comment_relations = array();
        if (!empty($article_comment_ids)) {
            $this->link->select('comment_id, LEFT (comment_content, 128) content')
                ->from($this->articles_comment)
                ->where(array('comment_id' => array('IN', $article_comment_ids)))
                ->stmt()->fetchAll(\PDO::FETCH_FUNC, function ($comment_id, $content) use (&$article_comment_relations) {
                    $article_comment_relations[$comment_id] = array(
                        'article_id' => $comment_id,
                        'content' => $content
                    );
                });
        }

        //数据对齐
        foreach ($act as &$a) {
            $relation_id = $a['relation_id'];
            $relation_data = array();
            switch ($a['action_type']) {
                case ActivityModule::POSTS:
                    if (isset($posts_relations[$relation_id])) {
                        $relation_data = $posts_relations[$relation_id];
                    }
                    break;

                case ActivityModule::POSTS_REPLY:
                    if (isset($reply_relations[$relation_id])) {
                        $relation_data = $reply_relations[$relation_id];
                    }
                    break;

                case ActivityModule::ARTICLE:
                case ActivityModule::ARTICLE_UP:
                case ActivityModule::ARTICLE_FOLLOW:
                    if (isset($article_relations[$relation_id])) {
                        $relation_data = $article_relations[$relation_id];
                    }
                    break;

                case ActivityModule::ARTICLE_COMMENT:
                    if (isset($article_comment_relations[$relation_id])) {
                        $relation_data = $article_comment_relations[$relation_id];
                    }
                    break;

                case ActivityModule::QUESTION_ANSWER:
                case ActivityModule::QUESTION_ANSWER_UP:
                    if (isset($answer_relations[$relation_id])) {
                        $relation_data = $answer_relations[$relation_id];
                    }
                    break;

                default:
                    $relation_data = array();
            }

            $a['images'] = array();
            if (isset($title_images[$a['title_id']])) {
                $a['images'] = $title_images[$a['title_id']];
            }

            $a['relation_data'] = $relation_data;
        }

        return $act;
    }

    /**
     * 动态存在则返回动态的id
     *
     * @param int $uid
     * @param int $title_id
     * @param int $acton_type
     * @param int $relation_id
     * @return bool
     */
    function has($uid, $title_id, $acton_type, $relation_id)
    {
        $actLog = $this->link->get($this->user_act_log, 'id', array(
            'uid' => $uid,
            'title_id' => $title_id,
            'action_type' => $acton_type,
            'relation_id' => $relation_id
        ));

        if ($actLog) {
            return $actLog['id'];
        }

        return false;
    }

    /**
     * 添加动态
     *
     * @param int $uid
     * @param int $action_type
     * @param int $title_id
     * @param int $relation_id
     * @return bool|mixed
     * @throws \Cross\Exception\CoreException
     */
    function add($uid, $title_id, $action_type, $relation_id)
    {
        return $this->link->add($this->user_act_log, array(
            'uid' => $uid,
            'title_id' => $title_id,
            'action_type' => $action_type,
            'relation_id' => $relation_id,
            'act_time' => TIME,
            'act_ip' => Helper::getLongIp()
        ));
    }

    /**
     * 删除动态
     *
     * @param int $act_id
     * @return bool
     * @throws \Cross\Exception\CoreException
     */
    function del($act_id)
    {
        if ($act_id) {
            return $this->link->del($this->user_act_log, array('id' => $act_id));
        }

        return true;
    }

    /**
     * 存在则删除
     *
     * @param int $uid
     * @param int $title_id
     * @param int $acton_type
     * @param int $relation_id
     * @return bool
     */
    function hasDelete($uid, $title_id, $acton_type, $relation_id)
    {
        return $this->link->del($this->user_act_log, array(
            'uid' => $uid,
            'title_id' => $title_id,
            'action_type' => $acton_type,
            'relation_id' => $relation_id
        ));
    }
}
