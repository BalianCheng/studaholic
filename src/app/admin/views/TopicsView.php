<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * TopicsView.php
 */

namespace app\admin\views;

/**
 * 话题视图控制器
 *
 * @Auth: cmz <393418737@qq.com>
 * Class TopicsView
 * @package app\admin\views
 */
class TopicsView extends ForumView
{
    function index($data = array())
    {
        $this->renderTpl('topics/index', $data);
    }

    function saveTopicUI($data = array())
    {
        $this->renderTpl('topics/fragment/modal_edit', $data);
    }

    function chiefEditor($data=array())
    {
        $this->renderTpl('topics/chiefEditor', $data);
    }

    function managerUI($data=array())
    {
        $this->renderTpl('topics/fragment/modal_manager', $data);
    }

    function saveRootTopicUI($data = array())
    {
        $this->renderTpl('topics/fragment/modal_root_edit', $data);
    }
}
