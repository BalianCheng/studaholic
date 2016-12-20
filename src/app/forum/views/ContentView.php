<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * ContentView.php
 */

namespace app\forum\views;

/**
 * 内容页面处理
 *
 * @Auth: cmz <393418737@qq.com>
 * Class ContentView
 * @package app\forum\views
 */
class ContentView extends ForumView
{
    /**
     * 问题
     *
     * @param array $data
     */
    function question($data = array())
    {
        $this->renderTpl('content/question', $data);
    }

    /**
     * 帖子
     *
     * @param array $data
     */
    function posts($data = array())
    {
        $this->renderTpl('content/posts', $data);
    }

    /**
     * 文章
     *
     * @param array $data
     */
    function article($data = array())
    {
        $this->renderTpl('content/article', $data);
    }

    /**
     * 编辑
     *
     * @param array $data
     */
    function edit($data = array())
    {
        $PV = new PublishView();
        $PV->setTplDir($this->getTplDir());
        $PV->setTplBasePath($this->getTplBasePath());

        $this->addRes('libs/select2/4.0.2/css/select2.min.css');
        switch ($data['type']) {
            case 'posts':
                $PV->posts($data);
                break;

            case 'article':
                $PV->article($data);
                break;

            case 'question':
                $PV->question($data);
                break;
        }
    }

    /**
     * 追加
     *
     * @param array $data
     */
    function append($data = array())
    {
        $this->renderTpl("publish/append", $data);
    }

    /**
     * 答案排序菜单
     *
     * @param int $answer_id
     * @param string $current
     */
    protected function answerOrderMenu($answer_id, $current)
    {
        $orderMembers = array(1 => '投票数', 2 => '时间');
        echo $this->makeOrderMenu($orderMembers, 'content:question', array('answer_id' => $answer_id), $current);
    }

    /**
     * 帖子回复排序菜单
     *
     * @param int $posts_id
     * @param string $current
     */
    protected function replyOrderMenu($posts_id, $current)
    {
        $orderMembers = array(1 => '支持数', 2 => '时间');
        echo $this->makeOrderMenu($orderMembers, 'content:posts', array('posts_id' => $posts_id), $current);
    }
}
