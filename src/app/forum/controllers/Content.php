<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Content.php
 */

namespace app\forum\controllers;

use app\forum\modules\following\FollowingModule;
use app\forum\modules\content\QuestionModule;
use app\forum\modules\content\ArticleModule;
use app\forum\modules\content\PostsModule;
use app\forum\modules\common\BaseModule;
use app\forum\modules\common\HitsModule;

/**
 * 内容控制器
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Content
 * @package app\forum\controllers
 */
class Content extends Forum
{

    /**
     * @return mixed
     */
    function index()
    {

    }

    /**
     * @cp_params question_id, order=1, p=1
     */
    function question()
    {
        $order = $this->params['order'];
        $question_id = (int)$this->params['question_id'];
        $QUESTION = new QuestionModule();
        $questionInfo = $QUESTION->getQuestionInfo($question_id, $this->uid);

        //1投票数 2时间
        $order_config = array(1 => true, 2 => true,);
        if (!isset($order_config[$order])) {
            $order = 1;
        }

        if (empty($questionInfo) || $questionInfo['status'] != 1) {
            $this->to();
        }

        if ($this->is_post()) {
            if (!$this->isLogin) {
                $this->to('user:login');
            }

            if (empty($_POST['content'])) {
                $this->to('content:question', array('question_id' => $question_id));
            }

            $QUESTION->saveAnswer($this->uid, $question_id, (bool)$questionInfo['best_answer_id'], $questionInfo['title_id'],
                $_POST['content'], $questionInfo['invite_id']);
            $this->to('content:question', array('question_id' => $question_id, 'order' => $order));
        }

        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 50,
            'link' => array('content:question', array('question_id' => $question_id, 'order' => $order)),
            'half' => 5,
        );

        //更新关注内容最后查看时间
        if ($questionInfo['follow_id']) {
            $FM = new FollowingModule();
            $FM->updateLastViewTime($questionInfo['follow_id']);
        }

        //处理点击统计
        $HITS = new HitsModule();
        $HITS->add(BaseModule::TYPE_QUESTION, array('question_id' => $question_id), $questionInfo['hits_update_time']);

        //相关内容
        $correlation_content = $QUESTION->getCorrelationContent($questionInfo['topic_ids'], BaseModule::TYPE_QUESTION);

        //正常答案列表
        $answer_list = $QUESTION->findQuestionAnswer($this->uid, $question_id, $order, $page);

        //最后一页显示获取非正常答案数量
        $blockedAnswerCount = 0;
        if ($page['p'] >= $page['total_page']) {
            $blockedAnswerCount = $QUESTION->getBlockAnswerCount($question_id);
        }

        $this->data['page'] = $page;
        $this->data['order'] = $order;
        $this->data['answer_list'] = $answer_list;
        $this->data['question_info'] = $questionInfo;
        $this->data['correlation_content'] = $correlation_content;
        $this->data['blocked_answer_count'] = $blockedAnswerCount;
        $this->data['publish_add_topic_name'] = $questionInfo['topics_names'];
        $this->data['content_type'] = BaseModule::TYPE_QUESTION;
        $this->display($this->data);
    }

    /**
     * @cp_params posts_id, order=1, cp=1, p=1
     */
    function posts()
    {
        $cp = (int)$this->params['cp'];
        $posts_id = (int)$this->params['posts_id'];
        $order = $this->params['order'];

        $POSTS = new PostsModule();
        $postsInfo = $POSTS->getPostsInfo($posts_id, $cp, $this->uid);
        if (!$postsInfo) {
            $this->to();
        }

        //1支持数 2时间
        $order_config = array(1 => true, 2 => true);
        if (!isset($order_config[$order])) {
            $order = 1;
        }

        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 50,
            'link' => array('content:posts', array('posts_id' => $posts_id, 'order' => $order, 'cp' => $cp)),
            'half' => 5,
        );

        if ($this->is_post()) {
            if (!$this->isLogin) {
                $this->to('user:login');
            }

            if (empty($_POST['content'])) {
                $this->to('content:posts', array('question_id' => $posts_id));
            }

            $POSTS->saveReply($this->uid, $posts_id, $postsInfo['title_id'], $_POST['content'], $postsInfo['invite_id']);
            $this->to('content:posts', array('posts_id' => $posts_id, 'order' => $order));
        }

        //更新关注内容最后查看时间
        if ($postsInfo['follow_id']) {
            $FM = new FollowingModule();
            $FM->updateLastViewTime($postsInfo['follow_id']);
        }

        //处理点击统计
        $HITS = new HitsModule();
        $HITS->add(BaseModule::TYPE_POSTS, array('posts_id' => $posts_id), $postsInfo['hits_update_time']);

        //相关内容
        $correlation_content = $POSTS->getCorrelationContent($postsInfo['topic_ids'], BaseModule::TYPE_POSTS);

        //回复列表
        $replyList = $POSTS->findReply($this->uid, $posts_id, $order, $page);

        //最后一页显示获取非正常回复数量
        $blockedReplyCount = 0;
        if ($page['p'] >= $page['total_page']) {
            $blockedReplyCount = $POSTS->getBlockReplyCount($posts_id);
        }

        $this->data['page'] = $page;
        $this->data['order'] = $order;
        $this->data['reply_list'] = $replyList;
        $this->data['posts_info'] = $postsInfo;
        $this->data['blocked_reply_count'] = $blockedReplyCount;
        $this->data['correlation_content'] = $correlation_content;
        $this->data['publish_add_topic_name'] = $postsInfo['topics_names'];
        $this->data['content_type'] = BaseModule::TYPE_POSTS;
        $this->display($this->data);
    }

    /**
     * @cp_params article_id, cp=1, p=1
     */
    function article()
    {
        $cp = (int)$this->params['cp'];
        $article_id = (int)$this->params['article_id'];

        $ARTICLE = new ArticleModule();
        $articleInfo = $ARTICLE->getArticleInfo($article_id, $cp, $this->uid);
        $category = $ARTICLE->getUserCategory($articleInfo['uid']);

        if (empty($articleInfo) || $articleInfo['status'] != 1) {
            $this->to();
        }

        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 50,
            'link' => array('content:article', array('article_id' => $article_id, 'cp' => $cp)),
            'half' => 3,
        );

        if ($this->is_post()) {
            if (!$this->isLogin) {
                $this->to('user:login');
            }

            if (empty($_POST['content'])) {
                $this->to('content:article', array('article_id' => $article_id));
            }

            $ARTICLE->saveComment($this->uid, $article_id, $articleInfo['title_id'], $_POST['content']);
            $this->toHash('content:article', array('article_id' => $article_id), '#article_form');
        }

        //更新关注内容最后查看时间
        if ($articleInfo['follow_id']) {
            $FM = new FollowingModule();
            $FM->updateLastViewTime($articleInfo['follow_id']);
        }

        //处理点击统计
        $HITS = new HitsModule();
        $HITS->add(BaseModule::TYPE_ARTICLE, array('article_id' => $article_id), $articleInfo['hits_update_time']);

        //相关内容
        $correlation_content = $ARTICLE->getCorrelationContent($articleInfo['topic_ids'], BaseModule::TYPE_ARTICLE);

        //评论列表
        $commentList = $ARTICLE->findComment($article_id, $page);

        //最后一页显示获取非正常回复数量
        $blockedCommentCount = 0;
        if ($page['p'] >= $page['total_page']) {
            $blockedCommentCount = $ARTICLE->getBlockCommentCount($article_id);
        }

        $this->data['article_info'] = $articleInfo;
        $this->data['comment_list'] = $commentList;
        $this->data['category'] = $category;
        $this->data['page'] = $page;
        $this->data['correlation_content'] = $correlation_content;
        $this->data['blocked_comment_count'] = $blockedCommentCount;
        $this->data['publish_add_topic_name'] = $articleInfo['topics_names'];
        $this->data['content_type'] = BaseModule::TYPE_ARTICLE;
        $this->display($this->data);
    }

    /**
     * 编辑内容
     *
     * @cp_params type, content_id, p=1
     */
    function edit()
    {
        if (!$this->isLogin) {
            $this->to();
        }

        $type = $this->params['type'];
        $this->checkParamsType($type);

        $p = (int)$this->params['p'];
        $content_id = (int)$this->params['content_id'];
        $content = $this->getContent($type, $content_id, $p);
        if (empty($content) || $content['can_edit'] != 1) {
            $this->to();
        }

        $this->makeCSRFToken();
        $this->data['content'] = $content;
        $this->data['save_type'] = $type;
        $this->data['type'] = $type;
        $this->display($this->data);
    }

    /**
     * 追加内容
     *
     * @cp_params type, content_id
     */
    function append()
    {
        if (!$this->isLogin) {
            $this->to();
        }

        $type = $this->params['type'];
        $this->checkParamsType($type);

        $content_id = (int)$this->params['content_id'];
        $content = $this->getContent($type, $content_id, 0);
        if (empty($content) || $content['can_append'] != 1) {
            $this->to();
        }

        $this->makeCSRFToken();
        $this->data['content'] = $content;
        $this->data['type'] = $type;
        $this->display($this->data);
    }

    /**
     * 编辑或追加内容前验证类型
     *
     * @param $type
     */
    private function checkParamsType($type)
    {
        $type_map = array(
            'posts' => 1,
            'article' => 1,
            'question' => 1,
        );

        if (!isset($type_map[$type])) {
            $this->to();
        }
    }

    /**
     * 获取内容
     *
     * @param string $type
     * @param int $content_id
     * @param int $p
     * @return mixed|string
     */
    private function getContent($type, $content_id, $p = 1)
    {
        switch ($type) {
            case 'posts':
                $POST = new PostsModule();
                $p = ($p == 0) ? $POST->getPostsContentMaxPage($content_id) : $p;
                $content = $POST->getPostsInfo($content_id, $p, $this->uid);
                break;

            case 'article':
                $ARTICLE = new ArticleModule();
                $p = ($p == 0) ? $ARTICLE->getArticleContentMaxPage($content_id) : $p;
                $content = $ARTICLE->getArticleInfo($content_id, $p, $this->uid);
                break;

            case 'question':
                $QUESTION = new QuestionModule();
                $content = $QUESTION->getQuestionInfo($content_id, $this->uid);
                break;

            default:
                $content = '';
        }

        return $content;
    }
}
