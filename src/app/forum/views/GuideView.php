<?php
/**
 * @Auth wonli <wonli@live.com>
 * GuideView.php
 */

namespace app\forum\views;

/**
 * @Auth wonli <wonli@live.com>
 * Class GuideView
 * @package app\forum\views
 */
class GuideView extends ForumView
{
    function __construct()
    {
        parent::__construct();
        $this->set(array(
            'layer' => 'guide'
        ));
    }

    /**
     * 补充信息
     *
     * @param array $data
     */
    function info($data = array())
    {
        $this->renderTpl('guide/info', $data);
    }

    /**
     * 关注列表
     *
     * @param array $data
     */
    function follow($data = array())
    {
        $this->renderTpl('guide/follow', $data);
    }

    /**
     * 第三方平台注册
     *
     * @param array $data
     */
    function platform_register($data = array())
    {
        $this->renderTpl('guide/platform_register', $data);
    }

    /**
     * 第三方平台绑定
     *
     * @param array $data
     */
    function platform_bind($data = array())
    {
        $this->renderTpl('guide/platform_bind', $data);
    }
}
