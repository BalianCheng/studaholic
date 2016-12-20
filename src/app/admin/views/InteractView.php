<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * InteractView.php
 */

namespace app\admin\views;

/**
 * 互动内容
 *
 * @Auth: cmz <393418737@qq.com>
 * Class InteractView
 * @package app\admin\views
 */
class InteractView extends ForumView
{
    /**
     * 问题答案
     *
     * @param array $data
     */
    function answer($data = array())
    {
        $this->renderTpl('interact/answer', $data);
    }

    /**
     * 帖子回复
     *
     * @param array $data
     */
    function reply($data = array())
    {
        $this->renderTpl('interact/reply', $data);
    }

    /**
     * 文章评论
     *
     * @param array $data
     */
    function comment($data = array())
    {
        $this->renderTpl('interact/comment', $data);
    }

}
