<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Connect.php
 */

namespace app\forum\controllers;

use app\forum\modules\account\AccountModule;
use app\forum\modules\account\PlatformModule;
use Cross\Core\Config;
use lib\OAuth\Server;

/**
 * 第三方登录
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Connect
 * @package app\forum\controllers
 */
class Connect extends Forum
{
    /**
     * @var Config
     */
    protected $OAuthConfig;

    function __construct()
    {
        parent::__construct();
        $this->OAuthConfig = $this->loadConfig('oauth.config.php');
    }

    /**
     * 第三方授权登录跳转
     *
     * @cp_params t=qq
     * @return mixed
     */
    function index()
    {
        $platform = $this->params['t'];
        if ($platform == 'weixin') {
            $config = $this->OAuthConfig->get('weixin');
            $config['call_back'] = $this->view->url("connect:weixin");
            $this->data['config'] = $config;
            $this->display($this->data, 'weixin');
        } else {
            switch ($platform) {
                case 'qq':
                case 'weibo':
                    $config = $this->OAuthConfig->get($platform);
                    $config['call_back'] = $this->view->url("connect:{$platform}");
                    $auth = new Server($platform, $config);
                    $url = $auth->getAuthorizeURL();
                    break;

                default:
                    $url = $this->view->url();
            }

            $this->redirect($url);
        }
    }

    /**
     * 解除绑定
     *
     * @cp_params t
     */
    function unbind()
    {
        $platform = $this->params['t'];
        if (!$this->isLogin) {
            $this->to();
        }

        $PM = new PlatformModule();
        $userPlatformBindInfo = $PM->getUserPlatformInfoByPlatformName($this->uid, $platform);
        if (empty($userPlatformBindInfo)) {
            $this->to();
        }

        //解除绑定
        $ret = $PM->delByBindID($userPlatformBindInfo['id']);
        if (!$ret) {
            $this->alertMessage(200460);
            $this->display($this->data, 'status');
        } else {
            $this->to('settings:platform');
        }
    }

    /**
     * 处理授权回调
     *
     * @param $action
     * @param array $args
     */
    function __call($action, $args = array())
    {
        $redirect = '';
        $code = $this->getOAuthCode();
        if (empty($code)) {
            $this->to();
        }

        $platform_user_data = array();
        $unionid = $openid = $access_token = $refresh_token = '';
        switch ($action) {
            case 'qq':
                $platform = PlatformModule::PLATFORM_QQ;
                $config = $this->OAuthConfig->get('qq');
                $config['call_back'] = $this->view->url('connect:qq');
                $OAuth = new Server('qq', $config);

                $response = $OAuth->getAccessToken($code);
                if (isset($response['access_token'])) {
                    $access_token = &$response['access_token'];
                    $openid_info = $OAuth->getOpenID($access_token);
                    if (isset($openid_info['openid'])) {
                        //平台用户信息
                        $openid = &$openid_info['openid'];
                        $userPlatformInfo = $OAuth->getUserInfo($access_token, $openid);
                        if (!empty($userPlatformInfo)) {
                            $platform_user_data['avatar'] = &$userPlatformInfo['figureurl_qq_2'];
                            $platform_user_data['nickname'] = &$userPlatformInfo['nickname'];
                            $platform_user_data['gender'] = &$userPlatformInfo['gender'];
                        }
                    }
                }
                break;

            case 'weibo':
                $platform = PlatformModule::PLATFORM_WEIBO;
                $config = $this->OAuthConfig->get('weibo');
                $config['call_back'] = $this->view->url('connect:weibo');
                $OAuth = new Server('weibo', $config);

                $response = $OAuth->getAccessToken($code);
                if (isset($response['access_token'])) {
                    $openid = &$response['uid'];
                    $access_token = &$response['access_token'];

                    $userPlatformInfo = $OAuth->getUserInfo($access_token, $openid);
                    if (!empty($userPlatformInfo)) {
                        $platform_user_data['avatar'] = &$userPlatformInfo['avatar_hd'];
                        $platform_user_data['nickname'] = &$userPlatformInfo['screen_name'];
                        $platform_user_data['gender'] = &$userPlatformInfo['gender'];
                    }
                }
                break;

            case 'weixin':
                $platform = PlatformModule::PLATFORM_WEIXIN;
                $config = $this->OAuthConfig->get('weixin');
                $config['call_back'] = $this->view->url('connect:weixin');
                $OAuth = new Server('weixin', $config);

                $response = $OAuth->getAccessToken($code);
                if (isset($response['access_token'])) {
                    $openid = &$response['openid'];
                    $access_token = &$response['access_token'];
                    $refresh_token = &$response['refresh_token'];
                    if (isset($response['unionid'])) {
                        $unionid = $response['unionid'];
                    }

                    $userPlatformInfo = $OAuth->getUserInfo($access_token, $openid);
                    if (!empty($userPlatformInfo)) {
                        $platform_user_data['avatar'] = &$userPlatformInfo['headimgurl'];
                        $platform_user_data['nickname'] = &$userPlatformInfo['nickname'];
                        $platform_user_data['gender'] = &$userPlatformInfo['sex'];
                    }
                }
                break;

            default:
                $this->alertMessage(200430);
                $this->display($this->data, 'status');
                return;
        }

        if (empty($platform_user_data) || empty($openid)) {
            $this->alertMessage(200441);
            $this->display($this->data, 'status');
            return;
        }

        $platform_data = array(
            'platform' => $platform,
            'openid' => $openid,
            'unionid' => $unionid,
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'avatar' => $platform_user_data['avatar'],
            'nickname' => self::getNickname($platform_user_data['nickname']),
            'gender' => self::getGender($platform_user_data['gender']),
        );

        $PM = new PlatformModule();
        if (!empty($platform_data['unionid'])) {
            $platform_info = $PM->getPlatformInfoByUnionID($platform_data['platform'], $platform_data['unionid']);
        } else {
            $platform_info = $PM->getPlatformInfoByOpenID($platform_data['platform'], $platform_data['openid']);
        }

        if ($this->isLogin) {
            //绑定第三方帐号平台
            if (empty($platform_info)) {
                $ret = $PM->bindPlatform($this->uid, $platform_data);
                if ($ret['status'] == 1) {
                    $this->to('settings:platform');
                } else {
                    $this->alertMessage($ret['status']);
                }
            } else {
                $this->alertMessage(200450);
            }

            $this->display($this->data, 'status');
        } else {
            //创建第三方平台帐号
            if (empty($platform_info)) {
                $create_info = $PM->createPlatformAccount($platform_data);
                if ($create_info['status'] != 1) {
                    $this->alertMessage($create_info['status']);
                    $this->display($this->data, 'status');
                    return;
                } else {
                    $this->setAuth('platform', array('id' => $create_info['message']['id']));
                    $this->to('guide:platform_register', array('redirect' => $redirect));
                }
            } elseif ($platform_info['uid'] <= 0) {
                //引导绑定或创建本地帐号
                $this->setAuth('platform', array('id' => $platform_info['id']));
                $this->to('guide:platform_register', array('redirect' => $redirect));
            } else {
                //获取用户信息并登录
                $user_info = $PM->getAccountInfoByUid($platform_info['uid'], 'uid, account, nickname, introduce, avatar, status');
                if ($user_info['status'] == PlatformModule::STATUS_BAN) {
                    $this->alertMessage(200330);
                    $this->display($this->data, 'status');
                } else {
                    $this->setAuth('u', $user_info);
                    if ($platform_info['access_token'] != $platform_data['access_token']) {
                        $PM->updatePlatformInfo($platform_info['id'], $platform_data);
                    }

                    //跳转
                    if (!empty($redirect)) {
                        $this->redirect($redirect);
                    } else {
                        $this->to();
                    }
                }
            }
        }
    }

    /**
     * 获取授权code
     *
     * @return mixed
     */
    private function getOAuthCode()
    {
        if (!empty($this->params['code'])) {
            $code = $this->params['code'];
        } else {
            $request_url = &$_SERVER['REQUEST_URI'];
            parse_str(parse_url($request_url, PHP_URL_QUERY), $request);
            $code = &$request['code'];
        }

        return $code;
    }

    /**
     * 获取性别
     *
     * @param string $gender 0,未知 1,男 2,女
     * @return int
     */
    private static function getGender($gender)
    {
        $genderMap = array(
            1 => AccountModule::GENDER_MALE, '男' => AccountModule::GENDER_MALE, 'm' => AccountModule::GENDER_MALE,
            2 => AccountModule::GENDER_MADAM, '女' => AccountModule::GENDER_MADAM, 'f' => AccountModule::GENDER_MADAM,
        );

        if (isset($genderMap[$gender])) {
            return $genderMap[$gender];
        }

        //未知
        return 0;
    }

    /**
     * 过滤昵称中的表情
     *
     * @param string $text
     * @return mixed
     */
    private static function getNickname($text)
    {
        $clean_text = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $text);
        $clean_text = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $clean_text);
        $clean_text = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $clean_text);
        $clean_text = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $clean_text);
        $clean_text = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $clean_text);

        return $clean_text;
    }
}
