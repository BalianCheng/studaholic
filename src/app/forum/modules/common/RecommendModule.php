<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * RecommendModule.php
 */

namespace app\forum\modules\common;

use app\forum\views\ForumView;

/**
 * 推荐
 *
 * @Auth: cmz <393418737@qq.com>
 * Class RecommendModule
 * @package modules\common
 */
class RecommendModule extends BaseModule
{
    /**
     * 获取推荐用户
     *
     * @param $login_uid
     * @param array $recommend_uid
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function recommendUser($login_uid, &$recommend_uid = array())
    {
        $auto_condition = $condition = array();
        $following_user = array();
        if ($login_uid > 0) {
            $auto_condition = $condition = array('uid' => array('<>', $login_uid));
            $this->link->select('following_uid')
                ->from($this->following_user)->where(array('uid' => $login_uid))
                ->stmt()->fetchAll(\PDO::FETCH_FUNC, function ($following_uid) use (&$following_user) {
                    $following_user[$following_uid] = $following_uid;
                });
        }

        //推荐用户
        $recommend = $this->link->getAll($this->recommend_user, '*', $condition, 'sort ASC', 1, 10);
        //创建主题最多的10个用户
        $auto_condition['status'] = 1;
        $auto_recommend = $this->link->getAll($this->title, 'count(1) c, uid', $auto_condition, 'c DESC', 'uid', 10);

        $user_list = array();
        $user_list_process = function ($data, &$user_list) {
            array_map(function ($r) use (&$user_list) {
                $user_list[$r['uid']] = $r['uid'];
            }, $data);
        };

        if (!empty($recommend)) {
            $user_list_process($recommend, $user_list);
        }

        if (!empty($auto_recommend)) {
            $user_list_process($auto_recommend, $user_list);
        }

        $uid_map = array();
        $FV = new ForumView();
        if (!empty($user_list)) {
            $this->link->select('uid, account, avatar, nickname, introduce')
                ->from($this->user)->where(array(
                    'uid' => array('IN', $user_list)
                ))->stmt()->fetchAll(\PDO::FETCH_FUNC, function ($uid, $account, $avatar, $nickname, $introduce)
                use (&$uid_map, $FV, $following_user) {
                    $uid_map[$uid] = array(
                        'uid' => $uid,
                        'account' => $account,
                        'following_status' => (int)isset($following_user[$uid]),
                        'homepage' => $FV->url('user:detail', array('account' => $account)),
                        'avatar' => $FV->resAbsoluteUrl($avatar),
                        'nickname' => $nickname,
                        'introduce' => $introduce
                    );
                });
        }

        $recommend_user = array();
        if (!empty($recommend)) {
            $recommend_data ['type'] = 'recommend';
            $recommend_data ['type_title'] = $FV->getRecommendUserTitle('recommend');
            foreach ($recommend as $r) {
                $recommend_uid[$r['uid']] = $r['uid'];
                $recommend_data['user_list'][] = $uid_map[$r['uid']];
            }
            $recommend_user[] = $recommend_data;
        }

        if (!empty($auto_recommend)) {
            $title_data ['type'] = 'content';
            $title_data ['type_title'] = $FV->getRecommendUserTitle('content');
            foreach ($auto_recommend as &$u) {
                $recommend_uid[$u['uid']] = $u['uid'];
                $title_data['user_list'][] = $uid_map[$u['uid']];
            }
            $recommend_user[] = $title_data;
        }

        //推荐用户id
        $recommend_uid = array_values($recommend_uid);

        return $recommend_user;
    }

    /**
     * 获取系统推荐用户
     *
     * @param int $limit
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function getSiteRecommendUser($limit = 5)
    {
        $query = $this->link->select('*')
            ->from("{$this->recommend_user}")
            ->orderBy('sort ASC');

        if ($limit > 0) {
            $query->limit($limit);
        }

        $ru = $query->getSQL(true);
        return $this->link->select('u.uid, u.account, u.nickname, u.introduce, u.avatar, ru.id recommend_id, ru.sort')
            ->from("({$ru}) ru LEFT JOIN {$this->user} u ON ru.uid=u.uid")
            ->stmt()->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 更新推荐信息（仅有排序）
     *
     * @param array $data
     * @return bool
     */
    function updateRecommendInfo(array $data)
    {
        if (!empty($data)) {
            foreach ($data as $id => $info) {
                $this->link->update($this->recommend_user, $info, array('id' => (int)$id));
            }
        }

        return true;
    }

    /**
     * 用户是否已经被推荐
     *
     * @param int $uid
     * @return bool
     */
    function isRecommend($uid)
    {
        $info = $this->link->get($this->recommend_user, '1', array('uid' => (int)$uid));
        return $info != false;
    }

    /**
     * 添加推荐用户
     *
     * @param int $uid
     * @return bool|mixed
     * @throws \Cross\Exception\CoreException
     */
    function addRecommendUid($uid)
    {
        return $this->link->add($this->recommend_user, array('uid' => (int)$uid));
    }

    /**
     * 删除推荐用户
     *
     * @param int $id
     * @return bool
     * @throws \Cross\Exception\CoreException
     */
    function delRecommendUser($id)
    {
        return $this->link->del($this->recommend_user, array('id' => (int)$id));
    }
}
