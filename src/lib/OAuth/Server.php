<?php

namespace lib\OAuth;

use Exception;

/**
 * @Auth: cmz <393418737@qq.com>
 * OAuth.php
 */
class Server
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $options;

    /**
     * 支持的客户端列表
     *
     * @var array
     */
    static $platformMap = array(
        'qq' => 'lib\OAuth\Platform\QQ',
        'weibo' => 'lib\OAuth\Platform\Weibo',
        'weixin' => 'lib\OAuth\Platform\Weixin',
    );

    /**
     * Server constructor.
     * @param $platform
     * @param array $options
     * @throws Exception
     */
    function __construct($platform, $options = array())
    {
        if (!isset(self::$platformMap[$platform])) {
            throw new Exception('不支持的第三方平台');
        }

        if (empty($options['app_id']) || empty($options['app_key'])) {
            throw new Exception('app_id 或 app_key 不能为空');
        }

        $app_id = $options['app_id'];
        $app_key = $options['app_key'];
        unset($options['app_id'], $options['app_key']);

        if (empty($options['call_back'])) {
            throw new Exception('请填写授权回调地址');
        }

        $platform = &self::$platformMap[$platform];
        $this->client = new $platform($app_id, $app_key, $options);
        $this->options = $options;
    }

    /**
     * 获取授权地址
     *
     * @return string
     */
    function getAuthorizeURL()
    {
        $call_back = $this->options['call_back'];
        unset($this->options['call_back']);

        return $this->client->makeAuthorizeURL($call_back, $this->options);
    }

    /**
     * 获取accessToken
     *
     * @param $code
     * @return string
     */
    function getAccessToken($code)
    {
        return $this->client->getAccessToken($code, 'code');
    }

    /**
     * 获取OPENID
     *
     * @param string $access_token
     * @return mixed
     */
    function getOpenID($access_token)
    {
        return $this->client->getOpenID($access_token);
    }

    /**
     * 获取平台用户信息
     *
     * @param string $access_token
     * @param string $openid
     * @return mixed
     */
    function getUserInfo($access_token, $openid)
    {
        return $this->client->getUserInfo($access_token, $openid);
    }
}
