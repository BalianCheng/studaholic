<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Publish.php
 */

namespace app\forum\controllers;

use app\forum\modules\publish\PublishModule;
use app\forum\modules\title\TitleModule;

/**
 * @Auth: cmz <393418737@qq.com>
 * Class Publish
 * @package app\forum\controllers
 */
class Publish extends WantLogin
{

    function __construct()
    {
        parent::__construct();
        if ($this->action != 'save' && $this->action != 'append') {
            $this->makeCSRFToken();
        }
    }

    /**
     * @return mixed
     */
    function index()
    {

    }

    /**
     * 提问
     *
     * @throws \Cross\Exception\CoreException
     */
    function question()
    {
        $this->data['save_type'] = 'question';
        $this->display($this->data);
    }

    /**
     * 文章
     *
     * @throws \Cross\Exception\CoreException
     */
    function article()
    {
        $this->data['save_type'] = 'article';
        $this->display($this->data);
    }

    /**
     * 帖子
     *
     * @throws \Cross\Exception\CoreException
     */
    function posts()
    {
        $this->data['save_type'] = 'posts';
        $this->display($this->data);
    }

    /**
     * 保存内容
     *
     * @cp_params type, csrf_token
     */
    function save()
    {
        $type = $this->params['type'];
        $csrf_token = $this->params['csrf_token'];

        $tokenRet = $this->checkCSRFToken($csrf_token);
        if (!$tokenRet) {
            $saveRet = $this->result(200201);
        } else {
            $PUBLISH = new PublishModule();
            switch ($type) {
                case 'question':
                    $saveRet = $PUBLISH->saveQuestion($this->uid, $_POST);
                    break;

                case 'posts':
                    $saveRet = $PUBLISH->savePosts($this->uid, $_POST);
                    break;

                case 'article':
                    $saveRet = $PUBLISH->saveArticle($this->uid, $_POST);
                    break;

                default:
                    $saveRet = $this->result(200200);
            }
        }

        $id_name = "{$type}_id";
        if ($saveRet['status'] == 1) {
            $p = (int)$this->postData('p');
            $params[$id_name] = $saveRet['message'][$id_name];
            if ($p > 0) {
                $params['p'] = $p;
            }

            $this->to("content:{$type}", $params);
        } else {
            $this->alertMessage($saveRet['status']);
            $this->display($this->data);
        }
    }

    /**
     * 内容追加
     *
     * @cp_params type, csrf_token
     */
    function append()
    {
        $type = $this->params['type'];
        $csrf_token = $this->params['csrf_token'];

        if (empty($_POST['title_id']) || !$this->is_post()) {
            $this->to();
        }

        //检查追加权限
        $TITLE = new TitleModule();
        $title_id = (int)$_POST['title_id'];
        $title_info = $TITLE->getTitleInfo($title_id, 'uid');
        if (empty($title_info) || $title_info['uid'] != $this->uid) {
            $this->to();
        }

        $tokenRet = $this->checkCSRFToken($csrf_token);
        if (!$tokenRet) {
            $appendRet = $this->result(200201);
        } else {
            $PUBLISH = new PublishModule();
            switch ($type) {
                case 'posts':
                    if (empty($_POST['posts_id'])) {
                        $appendRet = $this->result(200613);
                    } else {
                        $posts_id = (int)$_POST['posts_id'];
                        $content = $_POST['content'];
                        $appendRet = $PUBLISH->appendPostsContent($title_id, $posts_id, $content);
                    }
                    break;

                case 'article':
                    if (empty($_POST['article_id'])) {
                        $appendRet = $this->result(200612);
                    } else {
                        $article_id = (int)$_POST['article_id'];
                        $content = $_POST['content'];
                        $appendRet = $PUBLISH->appendArticleContent($title_id, $article_id, $content);
                    }
                    break;

                default:
                    $appendRet = $this->result(200200);
            }
        }

        $id_name = "{$type}_id";
        if ($appendRet['status'] == 1) {
            $this->to("content:{$type}", array($id_name => $appendRet['message'][$id_name]));
        } else {
            $this->alertMessage($appendRet['status']);
            $this->display($this->data);
        }
    }
}
