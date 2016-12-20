<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * User.php
 */

namespace app\forum\controllers;

use app\forum\modules\following\FollowingModule;
use app\forum\modules\account\AccountModule;
use app\forum\modules\common\BaseModule;
use app\forum\modules\invite\InviteModule;
use app\forum\modules\title\TitleModule;
use Cross\Core\Helper;

/**
 * 用户相关
 *
 * @Auth: cmz <393418737@qq.com>
 * Class User
 * @package app\forum\controllers
 */
class User extends Forum
{
    /**
     * @return mixed
     */
    function index()
    {
        $this->to('user:login');
    }

    /**
     * 个人资料
     *
     * @cp_params account, content_type=article, p=1
     */
    function detail()
    {
        $account = $this->params['account'];
        $content_type = $this->params['content_type'];
        if(empty($account)) {
            $this->to();
        }

        $AM = new AccountModule();
        $account_info = $AM->getAccountInfo($account);
        if (empty($account_info)) {
            $this->to();
        }

        //关注状态及统计
        $is_follow = false;
        $FOLLOW = new FollowingModule();
        $follow_statistics = $FOLLOW->getFollowStatistics($account_info['uid']);
        if ($this->uid > 0) {
            $is_follow = $FOLLOW->isFollowUser($this->uid, $account_info['uid']);
        }

        //用户内容
        $TITLE = new TitleModule();
        $allow_content_type = array(
            'posts' => BaseModule::TYPE_POSTS,
            'article' => BaseModule::TYPE_ARTICLE,
            'question' => BaseModule::TYPE_QUESTION,
        );

        if (!isset($allow_content_type[$content_type])) {
            $content_type = 'article';
        }

        $tab_data = array(
            'article' => '文章',
            'posts' => '帖子',
            'question' => '问题'
        );

        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 50,
            'link' => array('user:detail', array('account' => $account, 'content_type' => $content_type)),
            'half' => 5,
        );
        $content_list = $TITLE->findUserContentList($account_info['uid'], $allow_content_type[$content_type], $page);
        $this->data['uid'] = $this->uid;
        $this->data['page'] = $page;
        $this->data['is_follow'] = $is_follow;
        $this->data['tab_data'] = $tab_data;
        $this->data['current_tab_name'] = $tab_data[$content_type];
        $this->data['account_info'] = $account_info;
        $this->data['content_type'] = $content_type;
        $this->data['content_list'] = $content_list;
        $this->data['follow_statistics'] = $follow_statistics;
        $this->display($this->data);
    }

    /**
     * 登录
     *
     * @cp_params back
     */
    function login()
    {
        if ($this->isLogin) {
            $this->to();
        }

        $encrypt = false;
        if (!empty($this->params['back'])) {
            $encrypt = true;
            $back_url = $this->params['back'];
        } elseif ($ref = $this->request->getUrlReferrer()) {
            $host_info = $this->request->getHostInfo();
            $back_url = substr($ref, strlen($host_info));
        } else {
            $back_url = $this->request->getBaseUrl(false);
        }

        if ($this->is_post()) {
            $AM = new AccountModule();
            $account = $this->postData('account');
            $password = $this->postData('password');

            $ret = $AM->login($account, $password);
            if ($ret['status'] == 1) {
                if ($encrypt) {
                    $back_url = base64_decode(urldecode($back_url));
                }

                $userInfo = $ret['message'];
                $this->setAuth('u', $userInfo);
                $this->redirect($back_url);
            } else {
                $this->alertMessage($ret['status']);
            }
        }

        $this->data['back'] = $back_url;
        $this->data['encrypt'] = $encrypt;
        $this->display($this->data);
    }

    /**
     * 注册
     *
     * @cp_params back
     */
    function register()
    {
        if ($this->isLogin) {
            $this->to();
        }

        $encrypt = false;
        $invite = $this->siteConfig->get('invite');
        if (!empty($this->params['back'])) {
            $encrypt = true;
            $back_url = $this->params['back'];
        } elseif ($ref = $this->request->getUrlReferrer()) {
            $host_info = $this->request->getHostInfo();
            $back_url = substr($ref, strlen($host_info));
        } else {
            $back_url = $this->request->getBaseUrl(false);
        }

        if ($this->is_post()) {
            $AM = new AccountModule();
            $account = $this->postData('account');
            $password = $this->postData('password');
            $repeat_password = $this->postData('repeat_password');

            $status = 1;
            $invite_code_id = 0;
            if (empty($account)) {
                $status = 200301;
            } elseif ($password != $repeat_password) {
                $status = 200302;
            } elseif ($invite) {
                $invite_code = $this->postData('invite_code');
                $IM = new InviteModule();
                $ret = $IM->checkInviteCode($invite_code);
                if ($ret['status'] != 1) {
                    $status = $ret['status'];
                } else {
                    $invite_code_id = $ret['message']['id'];
                }
            }

            if ($status == 1) {
                $reg_ret = $AM->register($account, $password, $invite_code_id);
                if ($reg_ret['status'] == 1) {
                    $userInfo = $reg_ret['message'];
                    $this->setAuth('u', $userInfo);
                    if (!$encrypt) {
                        $back_url = base64_encode(urlencode($back_url));
                    }

                    $this->to('guide:info', array('redirect' => $back_url));
                } else {
                    $status = $reg_ret['status'];
                }
            }

            if ($status != 1) {
                $this->alertMessage($status);
            }
        }

        $this->data['back'] = $back_url;
        $this->data['invite'] = $invite;
        $this->data['encrypt'] = $encrypt;
        $this->display($this->data);
    }

    /**
     * 邀请注册
     *
     * @cp_params token
     */
    function invite()
    {
        $token = $this->params['token'];
        $tokenInfo = Helper::encodeParams($token, 'inviteRegister', 'decode');

        $IM = new InviteModule();
        $AM = new AccountModule();

        $invite_code_id = 0;
        $tokenData = json_decode($tokenInfo, true);
        if (!$tokenInfo || !$tokenData) {
            $this->data['status'] = 200421;
        } else {
            $accountInfo = $AM->getAccountInfoByUid($tokenData['u']);
            if (!$accountInfo || $accountInfo['status'] != 1) {
                $this->data['status'] = 200423;
            } else {
                $this->data['inviteUserInfo'] = $accountInfo;
            }

            if ($this->isLogin && ($accountInfo['uid'] != $this->uid)) {
                $this->to();
            }

            $inviteInfo = $IM->getUserInviteCode($tokenData['u']);
            $invite_code_id = $inviteInfo['id'];
            if (empty($inviteInfo) || $inviteInfo['invite_code'] != $tokenData['c']) {
                $this->data['status'] = 200422;
            }
        }

        if ($this->is_post() && $invite_code_id) {
            $account = $this->postData('account');
            $password = $this->postData('password');
            $repeat_password = $this->postData('repeat_password');

            if (empty($account)) {
                $this->data['status'] = 200301;
            } elseif ($password != $repeat_password) {
                $this->data['status'] = 200302;
            } else {

                $reg_ret = $AM->register($account, $password, $invite_code_id);
                if ($reg_ret['status'] == 1) {
                    $userInfo = $reg_ret['message'];
                    $this->setAuth('u', $userInfo);
                    $this->to('guide:info', array('redirect' => ''));
                } else {
                    $this->data['status'] = $reg_ret['status'];
                }
            }

            if ($this->data['status'] != 1) {
                $this->alertMessage($this->data['status']);
            }
        }

        $this->data['token'] = $token;
        if ($this->data['status'] != 1) {
            $this->alertMessage($this->data['status']);
        }

        $this->display($this->data);
    }

    /**
     * 退出登录
     */
    function logout()
    {
        $this->setAuth('u', null, -1);
        $this->return_referer();
    }
}
