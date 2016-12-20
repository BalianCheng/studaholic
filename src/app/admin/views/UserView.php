<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * UserView.php
 */

namespace app\admin\views;

/**
 * @Auth: cmz <393418737@qq.com>
 * Class UserView
 * @package app\admin\views
 */
class UserView extends ForumView
{
    function index($data)
    {
        $this->renderTpl('user/index', $data);
    }

    function recommend($data = array())
    {
        $this->renderTpl('user/recommend', $data);
    }

    function resetPassword($data)
    {
    }
}
