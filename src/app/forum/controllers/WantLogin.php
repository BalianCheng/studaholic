<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * WantLogin.php
 */

namespace app\forum\controllers;

use app\forum\modules\account\AccountModule;

/**
 * 验证登录权限
 *
 * @Auth: cmz <393418737@qq.com>
 * Class WantLogin
 * @package app\forum\controllers
 */
class WantLogin extends Forum
{
    function __construct()
    {
        parent::__construct();
        if (!$this->isLogin) {
            $back_url = urlencode(base64_encode($this->request->getCurrentUrl(false)));
            $this->to('user:login', array('back' => $back_url));
        } else {
            $AM = new AccountModule();
            $status = $AM->getUserStatus($this->uid);
            if (false === $status || $status == AccountModule::STATUS_BAN) {
                $this->setAuth('u', null, -1);
                $this->to('user:login');
            } elseif ($status == AccountModule::STATUS_UNFINISHED) {
                if ($this->controller != 'Guide' && $this->controller != 'platform_register') {
                    $this->to('guide:platform_register');
                }
            }
        }
    }

    /**
     * @return mixed
     */
    function index()
    {

    }
}
