<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * ConnectView.php
 */

namespace app\forum\views;

/**
 * 第三方登录视图控制器
 *
 * @Auth: cmz <393418737@qq.com>
 * Class ConnectView
 * @package app\forum\views
 */
class ConnectView extends ForumView
{
    function index()
    {

    }

    /**
     * 微信扫码登录
     *
     * @param array $data
     */
    function weixin($data = array())
    {
        $this->renderTpl('connect/weixin', $data);
    }

    /**
     * @param array $data
     */
    function status($data = array())
    {

    }
}
