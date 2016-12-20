<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * PublishView.php
 */

namespace app\forum\views;

/**
 * @Auth: cmz <393418737@qq.com>
 * Class PublishView
 * @package app\forum\views
 */
class PublishView extends ForumView
{
    function __construct()
    {
        parent::__construct();
        $this->addRes('libs/select2/4.0.2/css/select2.min.css');
    }

    /**
     * 发布问题
     *
     * @param array $data
     */
    function question($data = array())
    {
        if ($data['status'] != 1) {
            die($data['message']);
        }

        $content = array();
        if (isset($data['content'])) {
            $content = $data['content'];
        }

        $data['title'] = '标题';
        $data['title_placeholder'] = '尽量描述清楚对课程的疑问';
        $data['addition'] = array(
            'template' => 'publish/addition/question',
            'data' => $content,
        );

        $this->renderTpl('publish/publish', $data);
    }

    /**
     * 发布文章
     *
     * @param array $data
     */
    function article($data = array())
    {
        $content = array();
        if (isset($data['content'])) {
            $content = $data['content'];
        }

        $data['title'] = '文章标题';
        $data['title_placeholder'] = '请填写评课文章标题';
        $data['addition'] = array(
            'template' => 'publish/addition/article',
            'data' => $content,
        );
        $this->renderTpl('publish/publish', $data);
    }

    /**
     * 发布帖子
     *
     * @param array $data
     */
    function posts($data = array())
    {
        $content = array();
        if (isset($data['content'])) {
            $content = $data['content'];
        }

        $data['title'] = '帖子标题';
        $data['title_placeholder'] = '请填写帖子标题';
        $data['addition'] = array(
            'template' => 'publish/addition/posts',
            'data' => $content,
        );

        $this->renderTpl('publish/publish', $data);
    }

    /**
     * 保存内容
     *
     * @param array $data
     */
    function save($data = array())
    {

    }

    /**
     * 内容追加
     *
     * @param array $data
     */
    function append($data = array())
    {

    }
}
