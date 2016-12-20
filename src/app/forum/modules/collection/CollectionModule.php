<?php
namespace app\forum\modules\collection;

use app\forum\modules\common\BaseModule;
use app\forum\modules\title\TitleModule;

/**
 * @Auth: cmz <393418737@qq.com>
 * CollectionModule.php
 */
class CollectionModule extends BaseModule
{
    /**
     * 默认收藏夹名称
     *
     * @var string
     */
    protected $default_collection_name = '默认收藏夹';

    /**
     * 收藏或取消收藏
     *
     * @param int $uid
     * @param int $title_id
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function save($uid, $title_id)
    {
        $uid = (int)$uid;
        $title_id = (int)$title_id;
        $title_info = $this->link->get($this->title, 1, array('title_id' => $title_id));
        if (!$title_info) {
            return $this->result(200600);
        }

        $is_collection = $this->link->get($this->collections, 'id', array('uid' => $uid, 'title_id' => $title_id));
        if ($is_collection) {
            $del_ret = $this->link->del($this->collections, array('id' => $is_collection['id']));
            if ($del_ret) {
                return $this->result(1, array('act' => 0));
            } else {
                return $this->result(200601);
            }
        }

        $collection_category_id = $this->getCollectionCategoryID($uid);
        $ret = $this->link->add($this->collections, array(
            'uid' => $uid,
            'title_id' => $title_id,
            'category_id' => $collection_category_id,
            'collections_time' => TIME,
        ));

        if ($ret) {
            return $this->result(1, array('act' => 1));
        } else {
            return $this->result(200602);
        }
    }

    /**
     * 获取用户收藏夹
     *
     * @param int $uid
     * @return bool|mixed
     * @throws \Cross\Exception\CoreException
     */
    function getCollectionCategoryID($uid)
    {
        $uid = (int)$uid;
        $user_category = $this->link->get($this->collections_category, 'category_id', array('uid' => $uid, 'sort' => 1));
        if ($user_category) {
            return $user_category['category_id'];
        } else {
            $category_id = $this->link->add($this->collections_category, array(
                'uid' => $uid,
                'category_name' => $this->default_collection_name,
                'sort' => 1,
                'public' => 1,
                'create_time' => TIME
            ));

            return $category_id;
        }
    }

    /**
     * 获取用户收藏的主题
     *
     * @param int $uid
     * @param array $page
     * @return array
     */
    function findUserCollectionContent($uid, array &$page)
    {
        $uid = (int)$uid;
        $total = $this->link->get($this->collections, 'count(1) total_result', array('uid' => $uid));
        $page['result_count'] = $total['total_result'];

        $page['limit'] = max(1, (int)$page['limit']);
        $page['total_page'] = ceil($page['result_count'] / $page['limit']);

        $list = array();
        if ($page['p'] <= $page['total_page']) {

            $page['p'] = max(1, $page['p']);
            $start = ($page['p'] - 1) * $page['limit'];

            $title_sql = $this->link->select('title_id')
                ->from($this->collections)->where("uid={$uid}")->orderBy('id DESC')
                ->limit($start, $page['limit'])->getSQL(true);

            $TITLE = new TitleModule();
            $list = $TITLE->contentListDetail($title_sql);
        }

        return $list;
    }
}
