<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * TopicsView.php
 */

namespace app\forum\views;

/**
 * 话题视图控制器
 *
 * @Auth: cmz <393418737@qq.com>
 * Class TopicsView
 * @package app\forum\views
 */
class TopicsView extends ForumView
{
    /**
     * 话题首页列表
     *
     * @param array $data
     */
    function index($data = array())
    {
        $this->renderTpl('topics/index', $data);
    }

    /**
     * @param array $data
     */
    function detail($data = array())
    {
        $this->renderTpl('topics/detail', $data);
    }
}
