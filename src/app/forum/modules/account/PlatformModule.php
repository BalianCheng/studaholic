<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * PlatformModule.php
 */

namespace app\forum\modules\account;

use Cross\Core\Config;
use Cross\Core\Helper;
use Exception;

/**
 * 第三方平台帐号系统
 *
 * @Auth: cmz <393418737@qq.com>
 * Class PlatformModule
 * @package app\forum\modules\account
 */
class PlatformModule extends AccountModule
{
    /**
     * 平台名称到ID对应
     *
     * @var array
     */
    static $platformNameMap = array(
        'qq' => self::PLATFORM_QQ,
        'weibo' => self::PLATFORM_WEIBO,
        'weixin' => self::PLATFORM_WEIXIN,
    );

    /**
     * 根据第三方平台记录ID获取平台用户信息
     *
     * @param int $id
     * @param string $fields
     * @return mixed
     */
    function getPlatformInfoByID($id, $fields = '*')
    {
        return $this->link->get($this->user_openid, $fields, array(
            'id' => (int)$id,
        ));
    }

    /**
     * 从openid获取第三帐号信息
     *
     * @param string $platform
     * @param string $open_id
     * @param string $fields
     * @return mixed
     */
    function getPlatformInfoByOpenID($platform, $open_id, $fields = '*')
    {
        return $this->link->get($this->user_openid, $fields, array(
            'platform' => $platform,
            'openid' => $open_id
        ));
    }

    /**
     * 从unionid获取第三方帐号信息
     *
     * @param string $platform
     * @param string $union_id
     * @param string $fields
     * @return mixed
     */
    function getPlatformInfoByUnionID($platform, $union_id, $fields = '*')
    {
        return $this->link->get($this->user_openid, $fields, array(
            'platform' => $platform,
            'unionid' => $union_id
        ));
    }

    /**
     * 更新平台信息
     *
     * @param int $id
     * @param array $platform_info
     * @return bool
     */
    function updatePlatformInfo($id, $platform_info)
    {
        return $this->link->update($this->user_openid, $platform_info, array(
            'id' => (int)$id,
        ));
    }

    /**
     * 绑定帐号
     *
     * @param string $account
     * @param string $password
     * @param array $platform_info
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function bindAccountFromPlatformData($account, $password, array $platform_info)
    {
        if ($platform_info['uid'] > 0) {
            return $this->result(200456);
        }

        $loginRet = parent::login($account, $password);
        if ($loginRet['status'] != 1) {
            return $this->result($loginRet['status']);
        }

        $uid = $loginRet['message']['uid'];
        $bindInfo = $this->getUserPlatformInfoByPlatform($uid, $platform_info['platform']);
        if ($bindInfo) {
            return $this->result(200457);
        }

        $updateRet = $this->link->update($this->user_openid, array('uid' => $uid), array(
            'id' => $platform_info['id']
        ));

        if ($updateRet) {
            return $loginRet;
        }

        return $this->result(200451);
    }

    /**
     * 创建平台帐号
     *
     * @param array $platform_info
     * @return bool
     */
    function createPlatformAccount($platform_info)
    {
        if (empty($platform_info['platform'])) {
            return $this->result(200431);
        }

        if (empty($platform_info['openid'])) {
            return $this->result(200432);
        }

        if (empty($platform_info['access_token'])) {
            return $this->result(200433);
        }

        $platform_info['uid'] = 0;
        $platform_info['bind_time'] = TIME;
        $platform_id = $this->link->add($this->user_openid, $platform_info);
        if ($platform_id) {
            $platform_info['id'] = $platform_id;
            return $this->result(1, $platform_info);
        }

        return $this->result(200440);
    }

    /**
     * 从第三方平台创建本地帐号
     *
     * @param array $accountData
     * @param array $platform_info
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function createUserFromPlatformData(array $accountData, array $platform_info)
    {
        if (empty($accountData['account'])) {
            return $this->result(200301);
        }

        if (empty($accountData['nickname'])) {
            return $this->result(200322);
        }

        if ($platform_info['uid'] > 0) {
            return $this->result(200452);
        }

        $accountData['from_platform'] = $platform_info['platform'];
        $accountData['status'] = AccountModule::STATUS_NORMAL;
        $accountData['password'] = '';
        $accountData['salt'] = '';
        $accountData['register_ip'] = Helper::getLongIp();
        $accountData['register_time'] = TIME;

        try {
            $this->link->beginTA();
            $uid = $this->link->add($this->user, $accountData);
            if ($uid) {
                $this->link->update($this->user_openid, array('uid' => $uid), array(
                    'id' => $platform_info['id']
                ));
                $this->link->commit();
            } else {
                throw new Exception('register failed');
            }
        } catch (Exception $e) {
            $this->link->rollBack();
            return $this->result(200452);
        }

        return $this->result(1, array(
            'uid' => $uid,
            'avatar' => $accountData['avatar'],
            'account' => $accountData['account'],
            'nickname' => $accountData['nickname'],
            'introduce' => $accountData['introduce'],
            'status' => $accountData['status']
        ));
    }

    /**
     * 获取用户绑定的平台信息
     *
     * @param int $uid
     * @param string $platform_name
     * @return mixed
     */
    function getUserPlatformInfoByPlatformName($uid, $platform_name)
    {
        if (!isset(self::$platformNameMap[$platform_name])) {
            return array();
        }

        return $this->getUserPlatformInfoByPlatform($uid, self::$platformNameMap[$platform_name]);
    }

    /**
     * 用户绑定的平台信息
     *
     * @param int $uid
     * @param int $platform
     * @return mixed
     */
    function getUserPlatformInfoByPlatform($uid, $platform)
    {
        return $this->link->get($this->user_openid, '*', array(
            'uid' => (int)$uid,
            'platform' => (int)$platform,
        ));
    }

    /**
     * 删除绑定信息
     *
     * @param int $id
     * @return bool
     * @throws \Cross\Exception\CoreException
     */
    function delByBindID($id)
    {
        return $this->link->del($this->user_openid, array('id' => (int)$id));
    }

    /**
     * 绑定平台
     *
     * @param int $uid
     * @param array $platform_info
     * @return bool|mixed
     * @throws \Cross\Exception\CoreException
     */
    function bindPlatform($uid, array $platform_info)
    {
        $userInfo = $this->getAccountInfoByUid($uid);
        if ($userInfo['status'] == self::STATUS_BAN) {
            return $this->result(200330);
        }

        $bindInfo = $this->getUserPlatformInfoByPlatform($uid, $platform_info['platform']);
        if ($bindInfo) {
            return $this->result(200457);
        }

        $platform_info['uid'] = (int)$uid;
        $ret = $this->link->add($this->user_openid, $platform_info);
        if ($ret) {
            return $this->result(1);
        }

        return $this->result(200451);
    }

    /**
     * 获取用户平台帐号
     *
     * @param int $uid
     * @param bool $map
     * @return mixed
     */
    function getUserPlatformAccount($uid, $map = false)
    {
        $data = $this->link->getAll($this->user_openid, '*', array('uid' => ($uid)));
        if ($map) {
            $result = array();
            foreach ($data as $d) {
                $result[$d['platform']] = $d;
            }

            return $result;
        }

        return $data;
    }

    /**
     * 获取平台配置
     *
     * @return array
     */
    function getPlatformConfig()
    {
        $data = array();
        $configFile = $this->getFilePath('config::oauth.config.php');
        if (file_exists($configFile)) {
            $config = Config::load($configFile)->getAll();
            foreach ($config as $name => $conf) {
                if ($conf['app_id'] && $conf['app_key']) {
                    if (isset(self::$platformNameMap[$name])) {
                        $conf['platform'] = self::$platformNameMap[$name];
                    }

                    $data[$name] = $conf;
                }
            }
        }

        return $data;
    }
}
