<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Interact.php
 */

namespace app\forum\controllers;

use app\forum\modules\content\PostsModule;
use app\forum\modules\interact\AnswerCommentModule;
use app\forum\modules\content\QuestionModule;

/**
 * 互动内容
 * <pre>
 * 评论， 回复， 答案等
 * </pre>
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Interact
 * @package app\forum\controllers
 */
class Interact extends Forum
{
    /**
     * @return mixed
     */
    function index()
    {

    }

    /**
     * 答案详细列表
     *
     * @cp_params question_id, answer_id, p
     */
    function answer()
    {
        $p = (int)$this->params['p'];
        $answer_id = (int)$this->params['answer_id'];
        $question_id = (int)$this->params['question_id'];

        $QUESTION = new QuestionModule();
        $this->data['question_info'] = $QUESTION->getQuestionInfo($question_id, $this->uid);

        $page = array(
            'p' => $p,
            'limit' => 50,
            'half' => 5,
            'link' => array('interact:answer', array(
                'question_id' => $question_id,
                'answer_id' => $answer_id
            ))
        );

        //答案数据
        $this->initAnswerCommentData($answer_id, $page);
        if ($this->is_ajax_request()) {
            $this->view->commentList($this->data['comment_list']);
        } else {
            $this->display($this->data);
        }
    }

    /**
     * 帖子回复评论
     *
     * @cp_params posts_id, reply_id, cp=1, p=1
     */
    function reply()
    {
        $p = (int)$this->params['p'];
        $cp = (int)$this->params['cp'];
        $posts_id = (int)$this->params['posts_id'];
        $reply_id = (int)$this->params['reply_id'];

        $page = array(
            'p' => $p,
            'limit' => 50,
            'half' => 5,
            'link' => array('interact:reply', array(
                'posts_id' => $posts_id,
                'reply_id' => $reply_id,
                'cp' => $cp,
            ))
        );

        $PM = new PostsModule();
        $post_info = $PM->getPostsInfo($posts_id, $cp, $p);
        $reply_info = $PM->getReplyInfo($reply_id, $this->uid);
        if (empty($post_info) || empty($reply_info)) {
            $this->to('user:login');
        }

        if ($this->is_post()) {
            if ($this->uid < 0) {
                $this->to('user:login');
            }

            $content = $this->postData('content');
            if (empty($content)) {
                $this->alertMessage(200910, 'danger', 'col-md-9 col-centered');
            } else {
                $ret = $PM->saveReplyComment($this->uid, $reply_id, $content);
                if ($ret['status'] != 1) {
                    $this->alertMessage($ret['status'], 'warning', 'col-md-9 col-centered');
                } else {
                    $this->to($page['link'][0], $page['link'][1]);
                }
            }
        }

        //回复评论
        $reply_comment = $PM->findReplyCommend($reply_id, $page);
        $this->data['reply_comment'] = $reply_comment;
        $this->data['page'] = $page;

        if ($this->is_ajax_request()) {
            $this->view->commentList($this->data['reply_comment']);
        } else {
            $this->data['posts_info'] = $post_info;
            $this->data['reply_info'] = $reply_info;
            $this->display($this->data);
        }
    }

    /**
     * 初始化答案信息，和答案评论数据
     *
     * @param int $answer_id
     * @param array $page
     */
    private function initAnswerCommentData($answer_id, $page)
    {
        //基本验证
        $AC = new AnswerCommentModule();
        $answer_info = $AC->getAnswerInfo($answer_id);
        if (!$answer_info) {
            $this->to();
        }

        if ($this->is_post()) {
            if ($this->uid < 0) {
                $this->to('user:login');
            }

            $content = $this->postData('content');
            if (empty($content)) {
                $this->alertMessage(200910, 'danger', 'col-md-9 col-centered');
            } else {
                $ret = $AC->saveAnswerComment($this->uid, $answer_id, $content);
                if ($ret['status'] != 1) {
                    $this->alertMessage($ret['status'], 'warning', 'col-md-9 col-centered');
                } else {
                    $this->to($page['link'][0], $page['link'][1]);
                }
            }
        }

        if ($this->uid) {
            $this->data['stand'] = $AC->getUserStand($this->uid, $answer_id);
        } else {
            $this->data['stand'] = 0;
        }

        $comment_list = $AC->findAnswerComment($answer_id, $page);
        $this->data['page'] = $page;
        $this->data['answer_info'] = $answer_info;
        $this->data['comment_list'] = $comment_list;
    }
}
