<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Weibo.php
 */

namespace lib\OAuth\Platform;


use Cross\Core\Helper;
use lib\OAuth\Client;

class Weibo extends Client
{
    protected $authorizeUrl = 'https://api.weibo.com/oauth2/authorize';
    protected $accessTokenUrl = 'https://api.weibo.com/oauth2/access_token';

    /**
     * 获取平台OpenID
     *
     * @param string $access_token
     * @return mixed
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
     */
    function getUserInfo($access_token, $openid)
    {
        $url = 'https://api.weibo.com/2/users/show.json';
        $params['access_token'] = $access_token;
        $params['uid'] = $openid;

        $response = Helper::curlRequest($url, $params, 'GET');
        return json_decode($response, true);
    }
}
