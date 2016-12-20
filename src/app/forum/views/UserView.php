<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * UserView.php
 */

namespace app\forum\views;

/**
 * 用户相关视图控制器
 *
 * @Auth: cmz <393418737@qq.com>
 * Class UserView
 * @package app\forum\views
 */
class UserView extends ForumView
{

    function index($data = array())
    {
        $this->renderTpl('user/index', $data);
    }

    /**
     * 个人资料
     *
     * @param array $data
     */
    function detail($data = array())
    {
        $this->renderTpl('user/detail', $data);
    }

    /**
     * 登录
     *
     * @param array $data
     */
    function login($data = array())
    {
        $this->set(array(
            'layer' => 'enter'
        ));

        $back = &$data['back'];
        $encrypt = &$data['encrypt'];
        if (!$encrypt && !empty($back)) {
            $back = urlencode(base64_encode($back));
        }

        $data['back'] = $back;
        $this->renderTpl('user/login', $data);
    }

    /**
     * 注册
     *
     * @param array $data
     */
    function register($data = array())
    {
        $this->set(array(
            'layer' => 'enter'
        ));

        $back = &$data['back'];
        $encrypt = &$data['encrypt'];
        if (!$encrypt && !empty($back)) {
            $back = urlencode(base64_encode($back));
        }

        $data['back'] = $back;
        $this->renderTpl('user/register', $data);
    }

    /**
     * 邀请注册
     *
     * @param array $data
     */
    function invite($data = array())
    {
        $this->set(array(
            'layer' => 'enter'
        ));

        if (!empty($data['inviteUserInfo']) && $data['status'] == 1) {
            $this->renderTpl('user/invite_register', $data);
        }
    }

    /**
     * 退出登录
     *
     * @param array $data
     */
    function logout($data = array())
    {
        $this->renderTpl('user/logout', $data);
    }
}
