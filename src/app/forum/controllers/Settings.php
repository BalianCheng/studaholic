<?php
/**
 * @Auth wonli <wonli@live.com>
 * Setting.php
 */

namespace app\forum\controllers;

use app\forum\modules\account\AccountModule;
use app\forum\modules\account\PlatformModule;

/**
 * @Auth wonli <wonli@live.com>
 * Class Setting
 * @package app\forum\controllers
 */
class Settings extends WantLogin
{
    /**
     * @var array
     */
    private $tab_config = array(
        '' => '基本资料',
        'avatar' => '头像',
        'account' => '账号和密码',
        'collection' => '二维码',
        'platform' => '第三方帐号',
    );

    /**
     * Setting constructor.
     */
    function __construct()
    {
        parent::__construct();
        $tab = $this->action;
        if (!isset($this->tab_config[$tab])) {
            $tab = '';
        }

        if (empty($tab)) {
            $tpl = 'base';
        } else {
            $tpl = $tab;
        }

        $this->data['tpl'] = $tpl;
        $this->data['current_tab'] = $tab;
        $this->data['tab_config'] = $this->tab_config;
    }

    /**
     * 基本资料
     *
     * @return mixed
     */
    function index()
    {
        if ($this->is_post()) {
            $nickname = $this->postData('nickname');
            $introduce = $this->postData('introduce');
            $AM = new AccountModule();

            //更新昵称
            $update_data['introduce'] = $introduce;
            if ($nickname && ($nickname != $this->loginUser['nickname'])) {
                $ret = $AM->checkNickname($nickname);
                if ($ret['status'] != 1) {
                    $this->alertMessage($ret['status']);
                    $this->display($this->data);
                    return;
                } else {
                    $update_data['nickname'] = $nickname;
                }
            }

            $ret = $AM->updateUserInfo($this->uid, $update_data);
            if ($ret['status'] != 1) {
                $this->alertMessage($ret['status']);
            } else {
                $this->updateUserCookie($update_data);
                $this->to('settings');
            }
        }

        $this->display($this->data);
    }

    /**
     * 更新头像
     *
     * @throws \Cross\Exception\CoreException
     */
    function avatar()
    {
        $PM = new PlatformModule();
        $userPlatformAccount = $PM->getUserPlatformAccount($this->uid);
        if ($this->is_post()) {
            if (!empty($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])) {
                $avatar = $this->uploadAvatar($this->uid);
            } elseif (!empty($_POST['avatarSrc'])) {
                foreach ($userPlatformAccount as $p) {
                    if ($p['avatar'] == $_POST['avatarSrc']) {
                        $avatar = $p['avatar'];
                        break;
                    }
                }
            }

            if (!empty($avatar)) {
                $data = array('avatar' => $avatar);
                $PM->updateUserInfo($this->uid, $data);
                $this->updateUserCookie($data);
            }

            $this->to('settings:avatar');
        }

        $this->data['info'] = $this->loginUser;
        $this->data['platform'] = $userPlatformAccount;
        $this->display($this->data, 'index');
    }

    /**
     * 更新密码
     *
     * @throws \Cross\Exception\CoreException
     */
    function account()
    {
        $AM = new AccountModule();
        $accountInfo = $AM->getAccountInfoByUid($this->uid, '*');
        $this->data['account_info'] = $accountInfo;

        if ($this->is_post()) {
            $password = $this->postData('password');
            $new_password = $this->postData('new_password');
            $repeat_new_password = $this->postData('repeat_new_password');

            if (empty($new_password)) {
                $this->alertMessage(200411);
                $this->display($this->data, 'index');
                return;
            }

            if ($new_password != $repeat_new_password) {
                $this->alertMessage(200412);
                $this->display($this->data, 'index');
                return;
            }

            if ($accountInfo['from_platform'] != AccountModule::PLATFORM_LOCAL && empty($accountInfo['password'])) {
                $ret = $AM->updateUserPassword($this->uid, $new_password);
                if ($ret['status'] != 1) {
                    $this->alertMessage($ret['status']);
                } else {
                    $this->to('settings:account');
                }
            } else {
                if (empty($password)) {
                    $this->alertMessage(200410);
                    $this->display($this->data, 'index');
                    return;
                }

                $checkRet = $AM->checkUserPassword($this->uid, $password);
                if ($checkRet['status'] != 1) {
                    $this->alertMessage($checkRet['status']);
                } else {
                    $ret = $AM->updateUserPassword($this->uid, $new_password);
                    if ($ret['status'] != 1) {
                        $this->alertMessage($ret['status']);
                    } else {
                        $this->to('settings:account');
                    }
                }
            }
        }

        $this->display($this->data, 'index');
    }

    /**
     * 二维码
     */
    function collection()
    {
        $AM = new AccountModule();
        if ($this->is_post()) {
            if (!empty($_FILES['qr']) && !empty($_FILES['qr']['tmp_name'])) {
                $qr = $this->uploadQR($this->uid);
                $data = array('qr' => $qr);

                $AM->updateUserInfo($this->uid, $data);
            }
            $this->to('settings:collection');
        }
        $info = $AM->getAccountInfoByUid($this->uid, 'qr');
        $this->data['info'] = $info;
        $this->display($this->data, 'index');
    }

    /**
     * 第三方帐号
     */
    function platform()
    {
        $PM = new PlatformModule();
        $platformConfig = $PM->getPlatformConfig();
        $userPlatformAccount = $PM->getUserPlatformAccount($this->uid, true);

        //绑定状态
        foreach ($platformConfig as $name => &$config) {
            if (isset($userPlatformAccount[$config['platform']])) {
                $config['is_bind'] = 1;
            } else {
                $config['is_bind'] = 0;
            }
        }

        $this->data['platform'] = $platformConfig;
        $this->display($this->data, 'index');
    }
}
