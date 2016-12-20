<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * InteractView.php
 */

namespace app\forum\views;

/**
 * 互动内容视图
 *
 * @Auth: cmz <393418737@qq.com>
 * Class InteractView
 * @package app\forum\views
 */
class InteractView extends ForumView
{
    /**
     * @param $data
     */
    function reply($data)
    {
        $this->renderTpl('interact/reply', $data);
    }

    /**
     * @param array $data
     */
    function answer(array $data)
    {
        $this->renderTpl('interact/answer', $data);
    }

    /**
     * 评论内容列表
     *
     * @param $data
     */
    function commentList($data) {
        foreach ($data as $d) {
            $this->renderTpl('fragment/invite/answer_comment', $d);
        }
    }
}
