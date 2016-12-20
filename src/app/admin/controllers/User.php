<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * User.php
 */

namespace app\admin\controllers;


use app\forum\modules\account\AccountModule;
use app\forum\modules\common\RecommendModule;

/**
 * 用户管理
 *
 * @Auth: cmz <393418737@qq.com>
 * Class User
 * @package app\admin\controllers
 */
class User extends Forum
{
    /**
     * @cp_params p
     */
    function index()
    {
        $U = new AccountModule();
        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 20
        );

        $condition = array();
        if ($this->is_post()) {
            $condition = array($_POST['t'] => $_POST['key']);
            $this->data['t'] = $_POST['t'];
            $this->data['key'] = $_POST['key'];
            $page['limit'] = 1000;
        }

        $user_list = $U->userList($condition, $page);
        $this->data['page'] = $page;
        $this->data['user_list'] = $user_list;
        $this->data['addClass'] = 'sidebar-collapse';

        $this->display($this->data);
    }

    /**
     * 推荐关注用户
     */
    function recommend()
    {
        $RM = new RecommendModule();
        $recommendUser = $RM->getSiteRecommendUser(0);
        if ($this->is_post()) {
            $RM->updateRecommendInfo($_POST['info']);
            $this->to('user:recommend');
        }

        $this->data['recommendUser'] = $recommendUser;
        $this->display($this->data);
    }

    /**
     * 删除推荐
     *
     * @cp_params id
     */
    function delRecommendUser()
    {
        $id = (int)$this->params['id'];
        if ($id) {
            $RM = new RecommendModule();
            $RM->delRecommendUser($id);
        }

        $this->to('user:recommend');
    }

    /**
     * 添加推荐用户
     */
    function addRecommendUser()
    {
        if ($this->is_post() && !empty($_POST['uid'])) {
            $uid = (int)$_POST['uid'];

            $AM = new AccountModule();
            $uf = $AM->getAccountInfoByUid($uid);
            if (!$uf || $uf['status'] != 1) {
                $this->data['status'] = -1;
                $this->data['message'] = '用户不存在或已经被封号';
            } else {
                $RM = new RecommendModule();
                $ret = $RM->isRecommend($uid);
                if ($ret) {
                    //用户已经被推荐
                    $this->data['status'] = 0;
                    $this->data['message'] = '请不要重复推荐！';
                } else {
                    $RM->addRecommendUid($uid);
                    $this->data['status'] = 1;
                }
            }

            $this->display($this->data, 'JSON');
        } else {
            $this->to();
        }
    }

    /**
     * @cp_params uid, act
     */
    function ban()
    {
        $act = $this->params['act'];
        $uid = (int)$this->params['uid'];

        $statusValue = array('ban' => -1, 'unban' => 1);
        if (isset($statusValue[$act])) {
            $status = $statusValue[$act];
        } else {
            $status = -1;
        }

        $U = new AccountModule();
        $U->updateUserInfo($uid, array('status' => $status));
        $this->to('user:index');
    }

    /**
     * 重置密码
     */
    function resetPassword()
    {
        if (!$this->is_ajax_request() || !$this->is_post()) {
            $this->to();
        }

        $uid = (int)$_POST['uid'];
        $password = empty($_POST['password']) ? '123456' : $_POST['password'];

        $U = new AccountModule();
        $ret = $U->updateUserPassword($uid, $password);
        if ($ret) {
            $data['status'] = 1;
            $data['message'] = "已将密码重置为: {$password}";
        } else {
            $data['status'] = 0;
            $data['message'] = '修改密码失败!';
        }

        $this->dieJson($data);
    }

}
