<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * ArticleModule.php
 */

namespace app\forum\modules\content;

use app\forum\modules\activity\ActivityModule;
use app\forum\modules\title\TitleImagesModule;
use app\forum\modules\title\TitleModule;
use Cross\Core\Helper;

/**
 * 文章相关
 *
 * @Auth: cmz <393418737@qq.com>
 * Class ArticleModule
 * @package modules\content
 */
class ArticleModule extends ContentModule
{
    const COMMENT_NORMAL = 1; //正常
    const COMMENT_HIDDEN = -1; //隐蔽
    const COMMENT_BLOCKED = -2; //屏蔽

    /**
     * 默认分类名称
     *
     * @var string
     */
    private $default_category_name = '我的文章';

    /**
     * 文章基础信息
     *
     * @param int $article_id
     * @param string $fields
     * @return mixed
     */
    function getArticleBaseInfo($article_id, $fields = '*')
    {
        return $this->link->get($this->articles, $fields, array('article_id' => (int)$article_id));
    }

    /**
     * 文章信息
     *
     * @param int $article_id
     * @param int $p
     * @param int $uid
     * @return mixed
     */
    function getArticleInfo($article_id, $p, $uid)
    {
        $article_id = (int)$article_id;
        $base_sql = $this->link->select('article_id, summary, title_id, hits, hits_update_time, category_id')
            ->from($this->articles)->where("article_id={$article_id}")->getSQL(true);
        $sql_build = $this->link->select('t.*, aas.id stand, a.article_id, a.summary, a.hits, a.hits_update_time,
                aac.category_id, aac.category_name, a.article_id content_id, ac.content, ac.p, count(acc.id) content_page')
            ->from("({$base_sql}) a
            LEFT JOIN {$this->articles_content} ac ON a.article_id=ac.article_id and ac.p={$p}
            LEFT JOIN {$this->articles_content} acc ON a.article_id=acc.article_id
            LEFT JOIN {$this->articles_stand} aas ON a.article_id=aas.article_id and aas.uid={$uid}
            LEFT JOIN {$this->articles_category} aac ON a.category_id=aac.category_id
            LEFT JOIN {$this->title} t ON a.title_id=t.title_id")
            ->getSQL(true);

        return $this->getContentBaseExtendInfo($sql_build, $uid);
    }

    /**
     * 获取文章内容
     *
     * @param int $article_id
     * @param int $p
     * @return mixed
     */
    function getArticleContent($article_id, $p)
    {
        return $this->link->get($this->articles_content, '*', array('article_id' => (int)$article_id, 'p' => (int)$p));
    }

    /**
     * 所有文章评论列表
     *
     * @param array $condition
     * @param $page
     * @return mixed
     */
    function listComment($condition = array(), &$page)
    {
        return $this->link->find("{$this->articles_comment} c LEFT JOIN {$this->user} u ON c.uid=u.uid LEFT JOIN {$this->articles} a ON c.article_id=a.article_id",
            'c.*, u.account, u.nickname, a.title_id', $condition, 'comment_id DESC', $page
        );
    }

    /**
     * 更新评论状态
     *
     * @param int $comment_id
     * @param int $status
     * @return bool
     */
    function updateCommentStatus($comment_id, $status)
    {
        return $this->link->update($this->articles_comment, array('status' => (int)$status), array(
            'comment_id' => (int)$comment_id
        ));
    }

    /**
     * 获取已屏蔽评论数
     *
     * @param int $article_id
     * @return int
     */
    function getBlockCommentCount($article_id)
    {
        $info = $this->link->get($this->articles_comment, 'count(1) count', array(
            'article_id' => (int)$article_id, 'status' => array('<>', 1)
        ));

        if ($info) {
            return $info['count'];
        }

        return 0;
    }

    /**
     * 文章评论列表
     *
     * @param int $article_id
     * @param array $page
     * @param int $status
     * @param bool $use_limit
     * @return mixed
     */
    function findComment($article_id, &$page, $status = 1, $use_limit = true)
    {
        $article_id = (int)$article_id;
        if ($use_limit) {
            $comment = $this->link->find("{$this->articles_comment} c LEFT JOIN {$this->user} u ON c.uid=u.uid",
                'c.*, u.account, u.nickname, u.avatar, u.introduce',
                array('c.article_id' => $article_id, 'c.status' => $status), 'comment_id DESC', $page
            );
        } else {
            $comment = $this->link->getAll("{$this->articles_comment} c LEFT JOIN {$this->user} u ON c.uid=u.uid",
                'c.*, u.account, u.nickname, u.avatar, u.introduce',
                array('c.article_id' => $article_id, 'c.status' => $status), 'comment_id DESC');
        }

        return $comment;
    }

    /**
     * 保存文章评论
     *
     * @param int $uid
     * @param int $article_id
     * @param int $title_id
     * @param string $comment
     * @return bool|mixed
     * @throws \Cross\Exception\CoreException
     */
    function saveComment($uid, $article_id, $title_id, $comment)
    {
        $uid = (int)$uid;
        $title_id = (int)$title_id;
        $article_id = (int)$article_id;

        $images_list = array();
        $comment = $this->getContent($comment, $images_list);
        $comment_id = $this->link->add($this->articles_comment, array(
            'uid' => $uid,
            'article_id' => $article_id,
            'comment_content' => $comment,
            'comment_ip' => Helper::getLongIp(),
            'comment_time' => TIME
        ));

        if ($comment_id) {
            //增加互动次数
            $TM = new TitleModule();
            $TM->addInteractCount($title_id);

            //保存图片
            $TIM = new TitleImagesModule();
            $TIM->saveImages($title_id, $images_list, TitleImagesModule::LOCATION_INTERACT, $comment_id);

            //增加动态
            $ACTIVITY = new ActivityModule();
            $ACTIVITY->add($uid, $title_id, ActivityModule::ARTICLE_COMMENT, $article_id);
        }

        return $comment_id;
    }

    /**
     * 获取用户分类
     *
     * @param int $uid
     * @return mixed
     */
    function getUserCategory($uid)
    {
        if ($uid == 0) {
            return array();
        }

        $category = $this->link->getAll($this->articles_category, 'category_id, category_name', array('uid' => $uid), 'sort DESC');
        if (!$category) {
            $category_id = $this->link->add($this->articles_category, array(
                'uid' => $uid,
                'sort' => 1,
                'category_name' => $this->default_category_name,
                'create_time' => TIME
            ));

            $category[] = array(
                'category_id' => $category_id,
                'category_name' => $this->default_category_name
            );
        }

        return $category;
    }

    /**
     * 获取文章内容当前最大页数
     *
     * @param int $article_id
     * @return mixed
     */
    function getArticleContentMaxPage($article_id)
    {
        $maxInfo = $this->link->get($this->articles_content, 'MAX(p) p', array('article_id' => $article_id));
        return max(1, (int)$maxInfo['p']);
    }

    /**
     * 给文章投票
     *
     * @param int $uid
     * @param int $title_id
     * @param int $article_id
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function articleVote($uid, $title_id, $article_id)
    {
        $TITLE = new TitleModule();
        $ACT = new ActivityModule();
        $has = $this->link->get($this->articles_stand, 'id, act_id', array('uid' => $uid, 'article_id' => $article_id));
        if ($has) {
            $action = 0;
            $this->link->del($this->articles_stand, array('id' => $has['id']));

            //被赞次数-1
            $TITLE->minusUpCount($title_id);

            //删除动态
            $ACT->del($has['act_id']);
        } else {
            $action = 1;
            $act_id = $ACT->add($uid, $title_id, ActivityModule::ARTICLE_UP, $article_id);

            //被赞次数+1
            $TITLE->addUpCount($title_id);

            $this->link->add($this->articles_stand, array(
                'uid' => $uid,
                'article_id' => $article_id,
                'vote_time' => TIME,
                'act_id' => $act_id,
            ));
        }

        return $this->result(1, array('action' => $action));
    }
}
