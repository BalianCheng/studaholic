<?php
/**
 * @Auth wonli <wonli@live.com>
 * Guide.php
 */

namespace app\forum\controllers;

use app\forum\modules\account\PlatformModule;
use app\forum\modules\following\FollowingModule;
use app\forum\modules\account\AccountModule;

/**
 * 引导页面
 *
 * @Auth wonli <wonli@live.com>
 * Class Guide
 * @package app\forum\controllers
 */
class Guide extends Forum
{
    /**
     * 引导
     *
     * @cp_params step, redirect
     */
    function index()
    {

    }

    /**
     * 补充基本信息
     *
     * @cp_params redirect
     * @throws \Cross\Exception\CoreException
     */
    function info()
    {
        if (!$this->isLogin) {
            $this->to();
        }

        $redirect = $this->params['redirect'];
        if ($this->is_post()) {
            $avatar = '';
            $nickname = $this->postData('nickname');
            $introduce = $this->postData('introduce');

            if (!empty($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])) {
                $avatar = $this->uploadAvatar($this->uid);
            }

            $data = array();
            $cookie_data = $this->loginUser;
            if (!empty($nickname)) {
                $cookie_data['nickname'] = $data['nickname'] = $nickname;
            }

            if (!empty($introduce)) {
                $cookie_data['introduce'] = $data['introduce'] = $introduce;
            }

            if (!empty($avatar)) {
                $cookie_data['avatar'] = $data['avatar'] = $avatar;
            }

            if (!empty($data)) {
                $AM = new AccountModule();
                if (isset($data['nickname']) && $data['nickname'] != $this->loginUser['nickname']) {
                    $ret = $AM->checkNickname($nickname);
                    if ($ret['status'] != 1) {
                        $this->alertMessage($ret['status']);
                        $this->display($this->data);
                        return;
                    }
                }

                $updateRet = $AM->updateUserInfo($this->uid, $data);
                if ($updateRet['status'] != 1) {
                    $this->alertMessage($updateRet['status']);
                } else {
                    $this->setAuth('u', $cookie_data);
                    $this->to('guide:follow', array('redirect' => $redirect));
                }

            } else {
                $this->to('guide:follow', array('redirect' => $redirect));
            }
        }

        $this->display($this->data);
    }

    /**
     * 关注引导
     *
     * @cp_params redirect
     * @throws \Cross\Exception\CoreException
     */
    function follow()
    {
        if (!$this->isLogin) {
            $this->to();
        }

        $redirect = $this->params['redirect'];
        if ($this->is_post()) {
            $follow_data = $this->postData('follow_data');
            if (!empty($follow_data)) {
                $follow_user = explode(',', $follow_data);
                $follow_user = array_map('intval', $follow_user);
                $follow_user = array_filter($follow_user);

                $FM = new FollowingModule();
                $ret = $FM->multiFollowUser($this->uid, $follow_user);

                if ($ret['status'] == 1) {
                    if (!empty($redirect)) {
                        $redirect_url = base64_decode(urldecode($redirect));
                        $this->redirect($redirect_url);
                    } else {
                        $this->to();
                    }
                } else {
                    $this->alertMessage($ret['status']);
                }
            } else {
                $this->to();
            }
        }

        $this->display($this->data);
    }

    /**
     * 第三方平台注册引导
     *
     * @cp_params redirect
     */
    function platform_register()
    {
        $redirect = $this->params['redirect'];
        $this->checkPlatformAuthInfo($platformInfo);
        $this->data['platformInfo'] = $platformInfo;

        if ($this->is_post()) {
            $account = $this->postData('account');
            $nickname = $this->postData('nickname');
            $introduce = $this->postData('introduce');

            if (!empty($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])) {
                $avatar = $this->uploadAvatar($this->uid);
            } else {
                $avatar = $platformInfo['avatar'];
            }

            $PM = new PlatformModule();
            if (empty($account)) {
                $this->alertMessage(200301);
                $this->display($this->data);
                return;
            }

            //检测帐号是否被占用
            $ret = $PM->checkAccount($account);
            if ($ret['status'] != 1) {
                $this->alertMessage($ret['status']);
                $this->display($this->data);
                return;
            }

            //检测昵称
            $ret = $PM->checkNickname($nickname);
            if ($ret['status'] != 1) {
                $this->alertMessage($ret['status']);
                $this->display($this->data);
                return;
            }

            $data = array(
                'account' => $account,
                'nickname' => $nickname,
                'avatar' => $avatar,
                'introduce' => $introduce,
            );

            $createRet = $PM->createUserFromPlatformData($data, $platformInfo);
            if ($createRet['status'] != 1) {
                $this->alertMessage($createRet['status']);
            } else {
                $this->setAuth('u', $createRet['message']);
                $this->to('guide:follow', array('redirect' => $redirect));
            }
        }

        $this->display($this->data);
    }

    /**
     * 第三方平台绑定引导
     *
     * @cp_params redirect
     * @throws \Cross\Exception\CoreException
     */
    function platform_bind()
    {
        $redirect = $this->params['redirect'];
        $this->checkPlatformAuthInfo($platformInfo);
        $this->data['platformInfo'] = $platformInfo;

        if ($this->is_post()) {
            $account = $this->postData('account');
            $password = $this->postData('password');

            if (empty($account)) {
                $this->alertMessage(200454);
                $this->display($this->data);
                return;
            }

            if (empty($password)) {
                $this->alertMessage(200455);
                $this->display($this->data);
                return;
            }

            $PM = new PlatformModule();
            $bindRet = $PM->bindAccountFromPlatformData($account, $password, $platformInfo);
            if ($bindRet['status'] != 1) {
                $this->alertMessage($bindRet['status']);
            } else {
                $this->setAuth('u', $bindRet['message']);
                if ($redirect) {
                    $this->redirect($redirect);
                } else {
                    $this->to();
                }
            }
        }

        $this->display($this->data);
    }

    /**
     * 验证平台认证信息
     *
     * @param $platformInfo
     */
    private function checkPlatformAuthInfo(&$platformInfo)
    {
        $platformAuth = $this->getAuth('platform', true);
        if (empty($platformAuth)) {
            $this->to();
        }

        $PM = new PlatformModule();
        $platformInfo = $PM->getPlatformInfoByID($platformAuth['id']);
        if (empty($platformInfo)) {
            $this->to();
        }

        if ($platformInfo['uid'] > 0) {
            $this->to();
        }
    }
}
