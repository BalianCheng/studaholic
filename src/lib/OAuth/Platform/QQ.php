<?php

namespace lib\OAuth\Platform;

use Cross\Core\Helper;
use lib\OAuth\Client;
use Exception;

/**
 * @Auth: cmz <393418737@qq.com>
 * QQ.php
 */
class QQ extends Client
{
    protected $authorizeUrl = 'https://graph.qq.com/oauth2.0/authorize';
    protected $accessTokenUrl = 'https://graph.qq.com/oauth2.0/token';
    protected $responseTokenFormat = 'string';

    /**
     * 获取open_id
     *
     * @param string $access_token
     * @return mixed
     * @throws Exception
     */
    function getOpenID($access_token)
    {
        $url = "https://graph.qq.com/oauth2.0/me";
        $params['access_token'] = $access_token;

        $response = Helper::curlRequest($url, $params);
        if (strpos($response, 'callback') !== false) {
            $lPos = strpos($response, '(');
            $rPos = strrpos($response, ')');
            $response = trim(substr($response, $lPos + 1, $rPos - $lPos - 1));
        }

        if (($data = json_decode($response, true)) === false) {
            throw new Exception('获取用户ID失败');
        }

        return $data;
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
        $url = "https://graph.qq.com/user/get_user_info";
        $params['oauth_consumer_key'] = $this->app_id;
        $params['access_token'] = $access_token;
        $params['openid'] = $openid;
        $params['format'] = 'json';

        $response = Helper::curlRequest($url, $params, 'GET');
        if (($data = json_decode($response, true)) === false) {
            throw new Exception('获取用户信息');
        }

        return $data;
    }
}
