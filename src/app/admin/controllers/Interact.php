<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Ineract.php
 */

namespace app\admin\controllers;


use app\forum\modules\content\ArticleModule;
use app\forum\modules\content\PostsModule;
use app\forum\modules\content\QuestionModule;

/**
 * 互动内容控制器
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Interact
 * @package app\admin\controllers
 */
class Interact extends Forum
{
    /**
     * 问题答案
     *
     * @cp_params t=list, p=1
     */
    function answer()
    {
        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 30,
            'link' => array('interact:answer', array_filter($this->params))
        );

        switch ($this->params['t']) {
            case 'block':
                $condition = array('a.status' => QuestionModule::ANSWER_BLOCKED);
                break;

            case 'hidden':
                $condition = array('a.status' => QuestionModule::ANSWER_HIDDEN);
                break;

            case 'list':
            default:
                $condition = array('a.status' => QuestionModule::ANSWER_NORMAL);
        }

        $AM = new QuestionModule();
        $list = $AM->listAnswer($condition, $page);

        $this->data['list'] = $list;
        $this->data['page'] = $page;
        $this->data['addClass'] = 'sidebar-collapse';

        $this->display($this->data);
    }

    /**
     * 帖子回复
     *
     * @cp_params t=list, p=1
     */
    function reply()
    {
        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 30,
            'link' => array('interact:reply', array_filter($this->params))
        );

        switch ($this->params['t']) {
            case 'block':
                $condition = array('r.status' => PostsModule::REPLY_BLOCKED);
                break;

            case 'hidden':
                $condition = array('r.status' => PostsModule::REPLY_HIDDEN);
                break;

            case 'list':
            default:
                $condition = array('r.status' => PostsModule::REPLY_NORMAL);
        }

        $PM = new PostsModule();
        $list = $PM->listReply($condition, $page);

        $this->data['list'] = $list;
        $this->data['page'] = $page;
        $this->data['addClass'] = 'sidebar-collapse';

        $this->display($this->data);
    }

    /**
     * 文章评论
     *
     * @cp_params t=list, p=1
     */
    function comment()
    {
        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 30,
            'link' => array('interact:comment', array_filter($this->params))
        );

        switch ($this->params['t']) {
            case 'block':
                $condition = array('c.status' => ArticleModule::COMMENT_BLOCKED);
                break;

            case 'hidden':
                $condition = array('c.status' => ArticleModule::COMMENT_HIDDEN);
                break;

            case 'list':
            default:
                $condition = array('c.status' => ArticleModule::COMMENT_NORMAL);
        }

        $AM = new ArticleModule();
        $list = $AM->listComment($condition, $page);

        $this->data['list'] = $list;
        $this->data['page'] = $page;
        $this->data['addClass'] = 'sidebar-collapse';

        $this->display($this->data);
    }

    /**
     * 改变交互内容状态
     *
     * @cp_params status, type, id
     */
    function changeStatus()
    {
        $id = &$this->params['id'];
        $type = &$this->params['type'];
        $status = &$this->params['status'];

        if ($type && $id) {
            switch ($type) {
                case 'answer':
                    $QM = new QuestionModule();
                    $QM->updateAnswerStatus($id, $status);
                    break;

                case 'reply':
                    $PM = new PostsModule();
                    $PM->updateReplyStatus($id, $status);
                    break;

                case 'comment':
                    $AM = new ArticleModule();
                    $AM->updateCommentStatus($id, $status);
                    break;

            }
        }

        $this->return_referer();
    }
}
