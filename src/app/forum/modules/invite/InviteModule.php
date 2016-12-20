<?php
namespace app\forum\modules\invite;

use app\forum\modules\common\BaseModule;
use app\forum\modules\title\TitleModule;
use Cross\Core\Helper;

/**
 * 注册邀请相关
 *
 * @Auth: cmz <393418737@qq.com>
 * InviteModule.php
 */
class InviteModule extends BaseModule
{
    /**
     * 获取所有邀请码
     */
    function getAllInviteCode()
    {
        return $this->link->getAll($this->invite_code, '*', array('uid' => 0));
    }

    /**
     * 获取邀请码信息
     *
     * @param int $id
     * @param string $fields
     * @return mixed
     */
    function getInviteInfo($id, $fields = '*')
    {
        return $this->link->get($this->invite_code, $fields, array('id' => (int)$id));
    }

    /**
     * 删除邀请码
     *
     * @param int $id
     * @return bool
     * @throws \Cross\Exception\CoreException
     */
    function deleteInviteCode($id)
    {
        return $this->link->del($this->invite_code, array('id' => (int)$id));
    }

    /**
     * 增加使用计数
     *
     * @param int $id
     * @return bool
     */
    function addUseCount($id)
    {
        return $this->link->update($this->invite_code, 'use_count=use_count+1', array('id' => (int)$id));
    }

    /**
     * 重置用户邀请码
     *
     * @param int $uid
     * @return bool|mixed
     */
    function resetUserInviteCode($uid)
    {
        $userInviteCode = $this->getUserInviteCode($uid);
        if ($userInviteCode) {
            $ret = $this->link->update($this->invite_code, array('invite_code' => Helper::random(3)), array(
                'uid' => $uid
            ));
        } else {
            $ret = $this->createUserInviteCode($uid);
        }

        return $ret;
    }

    /**
     * 获取用户邀请码
     *
     * @param int $uid
     * @return mixed
     */
    function getUserInviteCode($uid)
    {
        $inviteInfo = $this->link->get($this->invite_code, '*', array('uid' => (int)$uid));
        if (!$inviteInfo) {
            $this->createUserInviteCode($uid);
            return $this->getUserInviteCode($uid);
        }

        return $inviteInfo;
    }

    /**
     * 创建用户邀请码
     *
     * @param int $uid
     * @return bool|mixed
     * @throws \Cross\Exception\CoreException
     */
    function createUserInviteCode($uid)
    {
        return $this->link->add($this->invite_code, array(
            'uid' => $uid,
            'invite_code' => Helper::random(3),
            'create_time' => TIME,
        ));
    }

    /**
     * 创建系统邀请码
     *
     * @param string $invite_code
     * @param string $comments
     * @return bool|mixed
     * @throws \Cross\Exception\CoreException
     */
    function createInviteCode($invite_code, $comments)
    {
        return $this->link->add($this->invite_code, array(
            'uid' => 0,
            'invite_code' => $invite_code,
            'comments' => self::getEntitiesData(strip_tags($comments)),
            'create_time' => TIME
        ));
    }

    /**
     * 更新邀请码信息
     *
     * @param int $id
     * @param $data
     * @return bool
     */
    function updateInviteInfo($id, $data)
    {
        return $this->link->update($this->invite_code, $data, array('id' => (int)$id));
    }

    /**
     * 获取已邀请的用户列表
     *
     * @param int $uid
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function getInviteUser($uid)
    {
        return $this->link->select('u.*')
            ->from("{$this->user} u LEFT JOIN {$this->invite_code} ic ON u.invite_code_id=ic.id")
            ->where(array('ic.uid' => (int)$uid))
            ->stmt()->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 验证系统邀请码
     *
     * @param string $invite_code
     * @return mixed
     */
    function checkInviteCode($invite_code)
    {
        $invite_code = self::getEntitiesData(strip_tags($invite_code));
        $invite_info = $this->link->get($this->invite_code, '*', array(
            'uid' => 0, 'invite_code' => $invite_code, 'status' => 1
        ));

        if ($invite_info) {
            return $this->result(1, $invite_info);
        }

        return $this->result(200420);
    }
}
