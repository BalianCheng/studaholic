<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * iClient.php
 */

namespace lib\OAuth;


use Cross\Core\Helper;
use Exception;

abstract class Client
{
    /**
     * @var string
     */
    protected $app_id;

    /**
     * @var string
     */
    protected $app_key;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * 授权地址
     *
     * @var string
     */
    protected $authorizeUrl = '';

    /**
     * 获取accessToken地址
     *
     * @var string
     */
    protected $accessTokenUrl = '';

    /**
     * token格式
     *
     * @var string
     */
    protected $responseTokenFormat = 'json';

    /**
     * iClient constructor.
     *
     * @param $app_id
     * @param $app_key
     * @param array $options
     */
    function __construct($app_id, $app_key, $options = array())
    {
        $this->app_id = $app_id;
        $this->app_key = $app_key;
        $this->options = $options;
    }

    /**
     * 授权地址
     *
     * @param string $callback_url
     * @param array $options
     * @return string
     */
    function makeAuthorizeURL($callback_url, $options = array())
    {
        $params = array(
                'response_type' => 'code',
                'client_id' => $this->app_id,
                'redirect_uri' => $callback_url,
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
            'client_id' => $this->app_id,
            'client_secret' => $this->app_key,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->options['call_back'],
        );

        $response = Helper::curlRequest($this->accessTokenUrl . '?' . http_build_query($params), $params);
        return $this->parseAccessToken($response, $this->responseTokenFormat);
    }

    /**
     * 获取平台OpenID
     *
     * @param string $access_token
     * @return mixed
     */
    abstract function getOpenID($access_token);

    /**
     * 获取用户信息
     *
     * @param string $access_token
     * @param string $openid
     * @return mixed
     */
    abstract function getUserInfo($access_token, $openid);

    /**
     * 按指定格式解析AccessToken接口返回的字符串
     *
     * @param string $response
     * @param string $responseTokenFormat
     * @return array|string
     * @throws Exception
     */
    protected function parseAccessToken($response, $responseTokenFormat)
    {
        $result = array();
        if ($responseTokenFormat == 'string') {
            parse_str($response, $result);
        } elseif ($responseTokenFormat == 'json') {
            $result = json_decode($response, true);
        }

        if (!is_array($result)) {
            throw new Exception($response);
        }

        return $result;
    }
}
