<?php
namespace app\forum\modules\account;

use app\forum\modules\following\FollowingModule;
use app\forum\modules\invite\InviteModule;
use app\forum\modules\common\BaseModule;
use Cross\Core\Helper;

/**
 * @Auth: cmz <393418737@qq.com>
 * AccountModule.php
 */
class AccountModule extends BaseModule
{
    /**
     * 性别
     */
    const GENDER_MALE = 1;
    const GENDER_MADAM = 2;

    /**
     * 帐号状态
     */
    const STATUS_BAN = -1; //被封号
    const STATUS_UNFINISHED = 0; //未完成(第三方登录未补充帐号信息时)
    const STATUS_NORMAL = 1; //正常

    /**
     * 注册平台
     */
    const PLATFORM_LOCAL = 1; //本地注册
    const PLATFORM_QQ = 2; //QQ
    const PLATFORM_WEIBO = 3; //微博
    const PLATFORM_WEIXIN = 4; //微信

    /**
     * 验证账号是否已存在
     *
     * @param string $account
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function checkAccount($account)
    {
        $account = self::getEntitiesData(strip_tags($account));
        $account_length = strlen($account);
        if ($account_length < 2 || $account_length > 12) {
            return $this->result(200304);
        }

        $match = preg_match("/^[a-zA-Z0-9]\w+$/", $account);
        if (!$match) {
            return $this->result(200305);
        }

        $account_info = $this->link->get($this->user, '1', array('account' => $account));
        if ($account_info) {
            return $this->result(200300);
        }

        return $this->result(1);
    }

    /**
     * 检查昵称是否已经被使用
     *
     * @param string $nickname
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function checkNickname($nickname)
    {
        $nickname = self::getEntitiesData(strip_tags($nickname));
        $nickname_length = Helper::strLen($nickname);
        if ($nickname_length < 2 || $nickname_length > 12) {
            return $this->result(200320);
        }

        $account_info = $this->link->get($this->user, '1', array('nickname' => $nickname));
        if ($account_info) {
            return $this->result(200321);
        }

        return $this->result(1);
    }

    /**
     * 检查用户密码
     *
     * @param int $uid
     * @param string $password
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function checkUserPassword($uid, $password)
    {
        $userInfo = $this->link->get($this->user, '*', array('uid' => $uid));
        if (empty($userInfo)) {
            return $this->result(200401);
        }

        $salt = $userInfo['salt'];
        $userPassword = $userInfo['password'];

        $inputPassword = $this->genPassword($password, $salt);
        if ($userPassword == $inputPassword) {
            return $this->result(1);
        }

        return $this->result(200402);
    }

    /**
     * 用户登录
     *
     * @param int $account
     * @param int $password
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function login($account, $password)
    {
        $userInfo = $this->link->get($this->user, '*', array('account' => $account));
        if (empty($userInfo)) {
            return $this->result(200401);
        }

        if ($userInfo['status'] == self::STATUS_BAN) {
            return $this->result(200330);
        }

        $salt = $userInfo['salt'];
        $userPassword = $userInfo['password'];

        $inputPassword = $this->genPassword($password, $salt);
        if ($userPassword == $inputPassword) {
            //更新最近登陆信息
            $this->link->update($this->user, array(
                'last_login_time' => TIME,
                'last_login_ip' => Helper::getLongIp()
            ), array('uid' => $userInfo['uid']));

            return $this->result(1, array(
                'uid' => $userInfo['uid'],
                'avatar' => $userInfo['avatar'],
                'account' => $userInfo['account'],
                'nickname' => $userInfo['nickname'],
                'introduce' => $userInfo['introduce'],
                'status' => $userInfo['status']
            ));
        } else {
            return $this->result(200402);
        }
    }

    /**
     * 用户注册
     *
     * @param string $account
     * @param string $password
     * @param int $invite_code_id
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function register($account, $password, $invite_code_id = 0)
    {
        $checkAccount = $this->checkAccount($account);
        if ($checkAccount['status'] != 1) {
            return $checkAccount;
        }

        $salt = Helper::random(16);
        $password = self::genPassword($password, $salt);

        $accountData = array(
            'account' => $account,
            'nickname' => $account,
            'introduce' => '',
            'invite_code_id' => (int)$invite_code_id,
            'password' => $password,
            'salt' => $salt,
            'avatar' => 'avatar/default.png',
            'register_ip' => Helper::getLongIp(),
            'register_time' => TIME,
            'from_platform' => self::PLATFORM_LOCAL,
            'status' => self::STATUS_NORMAL,
        );

        $uid = $this->link->add($this->user, $accountData);
        if ($uid) {
            $accountData['uid'] = $uid;
            if ($invite_code_id > 0) {
                $IM = new InviteModule();
                $inviteInfo = $IM->getInviteInfo($invite_code_id);

                //增加邀请码使用次数
                $IM->addUseCount($invite_code_id);

                //关注自己和邀请者
                if ($inviteInfo['uid'] > 0) {
                    $this->link->add($this->following_user, array(
                        'fields' => 'uid, following_uid, following_type, following_time',
                        'values' => array(
                            array($uid, $uid, FollowingModule::SYSTEM_FOLLOW, TIME),
                            array($uid, $inviteInfo['uid'], FollowingModule::SYSTEM_FOLLOW, TIME)
                        ),
                    ), true);
                }
            } else {
                $this->link->add($this->following_user, array(
                    'uid' => $uid,
                    'following_uid' => $uid,
                    'following_type' => FollowingModule::SYSTEM_FOLLOW,
                    'following_time' => TIME,
                ));
            }

            return $this->result(1, array(
                'uid' => $accountData['uid'],
                'avatar' => $accountData['avatar'],
                'account' => $accountData['account'],
                'nickname' => $accountData['nickname'],
                'status' => self::STATUS_NORMAL,
                'introduce' => ''
            ));
        }

        return $this->result(200303);
    }

    /**
     * 验证帐号状态
     *
     * @param int $uid
     * @return bool
     */
    function getUserStatus($uid)
    {
        $userInfo = $this->link->get($this->user, 'status', array('uid' => $uid));
        if ($userInfo) {
            return $userInfo['status'];
        }

        return false;
    }

    /**
     * 根据昵称查询用户
     *
     * @param string $nickname
     * @return mixed
     */
    function findAccountByNickname($nickname)
    {
        return $this->link->getAll($this->user, 'uid, account, nickname, introduce, avatar', array(
            'nickname' => array('LIKE', "%{$nickname}%")
        ));
    }

    /**
     * 获取用户基本信息
     *
     * @param string $account
     * @param string $fields
     * @return mixed
     */
    function getAccountInfo($account, $fields = 'uid, account, nickname, introduce, avatar, status')
    {
        return $this->link->get($this->user, $fields, array('account' => $account));
    }

    /**
     * @param $uid
     * @param string $fields
     * @return mixed
     */
    function getAccountInfoByUid($uid, $fields = 'uid, account, nickname, introduce, avatar, status')
    {
        return $this->link->get($this->user, $fields, array('uid' => (int)$uid));
    }

    /**
     * 更新用户资料
     *
     * @param int $uid
     * @param array $data
     * @return array|string
     */
    function updateUserInfo($uid, array $data)
    {
        $ret = $this->link->update($this->user, $data, array('uid' => (int)$uid));
        if ($ret) {
            return $this->result(1);
        }

        return $this->result(200310);
    }

    /**
     * 更新用户密码
     *
     * @param int $uid
     * @param string $password
     * @return array|string
     */
    function updateUserPassword($uid, $password)
    {
        $uid = (int)$uid;
        $salt = Helper::random(16);
        $password = self::genPassword($password, $salt);

        return $this->updateUserInfo($uid, array(
            'salt' => $salt,
            'password' => $password
        ));
    }

    /**
     * 获取用户昵称
     *
     * @param int $uid
     * @return mixed
     */
    function getUserNickname($uid)
    {
        static $cache = null;
        if (!isset($cache[$uid])) {
            $info = $this->link->get($this->user, 'nickname', array('uid' => $uid));
            $cache[$uid] = $info['nickname'];
        }

        return $cache[$uid];
    }

    /**
     * 用户列表
     *
     * @param array $condition
     * @param array $page
     * @param string $fields
     * @return mixed
     */
    function userList($condition = array(), &$page = array(), $fields = '*')
    {
        return $this->link->find($this->user, $fields, $condition, 'uid DESC', $page);
    }

    /**
     * 用户总数
     *
     * @return mixed
     */
    function getTotalUser()
    {
        $total = $this->link->get($this->user, 'count(1) count', array('status' => self::STATUS_NORMAL));
        return $total['count'];
    }

    /**
     * 获取注册人数信息
     *
     * @param string $start_time
     * @param string $end_time
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function getRegisterCollectInfo($start_time, $end_time)
    {
        return $this->link->select('DATE(FROM_UNIXTIME(register_time)) as date, COUNT(1) as count')
            ->from($this->user)->where(array('register_time > ? AND register_time < ?', array($start_time, $end_time)))
            ->groupBy('DATE(FROM_UNIXTIME(register_time))')->orderBy('date DESC')->stmt()->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 获取被封号用户uid列表
     *
     * @return string
     * @throws \Cross\Exception\CoreException
     */
    function getBanedAccountList()
    {
        return $this->link->select('uid')->from($this->user)
            ->where(array('status' => self::STATUS_BAN))->stmt()->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * 生成密码
     *
     * @param string $password
     * @param string $salt
     * @return string
     */
    protected static function genPassword($password, $salt)
    {
        return md5(sha1($password . $salt) . $salt);
    }

}
