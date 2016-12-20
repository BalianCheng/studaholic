<?php

namespace lib\OAuth\Platform;

use Cross\Core\Helper;
use lib\OAuth\Client;
use Exception;

/**
 * @Auth: cmz <393418737@qq.com>
 * QQ.php
 */
class Weixin extends Client
{
    protected $authorizeUrl = 'https://open.weixin.qq.com/connect/qrconnect';
    protected $accessTokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    /**
     * 获取授权地址
     *
     * @param string $callback_url
     * @param array $options
     * @return string
     */
    function makeAuthorizeURL($callback_url, $options = array())
    {
        $params = array(
                'response_type' => 'code',
                'appid' => $this->app_id,
                'redirect_uri' => $callback_url,
                'scope' => 'snsapi_login',
                'state' => NULL,
            ) + $options;

        return $this->authorizeUrl . '?' . http_build_query($params);
    }

    /**
     * 获取access token
     *
     * @param string $key
     * @param string $type
     * @return array|string
     * @throws Exception
     */
    function getAccessToken($key, $type = 'code')
    {
        if ($type != 'code') {
            return '';
        }

        $params = array(
            'code' => $key,
            'appid' => $this->app_id,
            'secret' => $this->app_key,
            'grant_type' => 'authorization_code'
        );

        $response = Helper::curlRequest($this->accessTokenUrl . '?' . http_build_query($params), $params);
        return $this->parseAccessToken($response, $this->responseTokenFormat);
    }

    /**
     * 获取open_id
     *
     * @param string $access_token
     * @return mixed
     * @throws Exception
     */
    function getOpenID($access_token)
    {

    }

    /**
     * 获取用户信息
     *
     * @param string $access_token
     * @param string $openid
     * @return mixed
     * @throws Exception
     */
    function getUserInfo($access_token, $openid)
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo';
        $params = array(
            'access_token' => $access_token,
            'openid' => $openid,
        );

        $response = Helper::curlRequest($url . '?' . http_build_query($params), $params);
        return $this->parseAccessToken($response, $this->responseTokenFormat);
    }
}
