<?php
namespace app\forum\modules\topic;

use app\forum\modules\common\BaseModule;
use app\forum\modules\title\TitleModule;
use lib\Tree\Tree;

/**
 * @Auth: cmz <393418737@qq.com>
 * TopicModule.php
 */
class TopicModule extends BaseModule
{
    /**
     * 获取推荐话题
     *
     * @param string $fields
     * @param int $limit 等于0时取所有话题, 大于0时取指定数量的话题
     * @return mixed
     */
    function getRecommendTopic($fields = '*', $limit = 0)
    {
        $condition = array('as_recommend' => 1, 'parent_id' => array('<>', 0));
        if ($limit > 0) {
            return $this->link->getAll($this->topic, $fields, $condition, 'sort ASC', 1, $limit);
        } else {
            return $this->link->getAll($this->topic, $fields, $condition, 'sort ASC');
        }
    }

    /**
     * 获取推荐话题及该话题下最新内容
     *
     * @param int $limit
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function getRecommendTopicNewTopic($limit)
    {
        $result = array();
        $recommend_topics = $this->getRecommendTopic('*', $limit);
        if (!empty($recommend_topics)) {
            $topic_ids = array();
            $recommend_topics_map = array();
            foreach ($recommend_topics as $topic) {
                $topic_ids[] = $topic['topic_id'];
                $recommend_topics_map[$topic['topic_id']] = $topic;
            }

            //获取话题下最新内容
            $title_id_list = array();
            $topic_title_map = array();
            $topic_ids_string = implode(',', $topic_ids);
            $topic_ids = array();
            $query = $this->link->select('tt.topic_id, max(tt.title_id) title_id')
                ->from("{$this->topic_title_id} tt LEFT JOIN {$this->title} t ON tt.title_id=t.title_id")
                ->where("tt.topic_id IN({$topic_ids_string}) AND t.status=1")->groupBy('tt.topic_id');

            $query->stmt()->fetchAll(\PDO::FETCH_FUNC, function ($topic_id, $title_id) use (&$topic_title_map, &$title_id_list, &$topic_ids) {
                $topic_ids[] = $topic_id; //重置话题ID列表
                $title_id_list[] = $title_id;
                $topic_title_map[$topic_id] = $title_id;
            });

            //获取话题被关注数量
            $follow_info_map = array();
            if (!empty($topic_ids)) {
                $follow_info_map = $this->link->select('topic_id, count(uid) follow_count')->from($this->following_topic)
                    ->where(array('topic_id' => array('IN', $topic_ids)))->groupBy('topic_id')
                    ->stmt()->fetchAll(\PDO::FETCH_KEY_PAIR);
            }

            //获取指定的ID的标题信息
            $title_info_maps = array();
            if (!empty($title_id_list)) {
                $title_id_list_string = implode(',', $title_id_list);
                $simpleContentListDetailQuery = $this->link->select('title_id')->from($this->title)
                    ->where("title_id IN({$title_id_list_string})")->getSQL(true);

                $TM = new TitleModule();
                $title_info_list = $TM->simpleContentListDetail($simpleContentListDetailQuery, false);
                foreach ($title_info_list as $title_info) {
                    $title_info_maps[$title_info['title_id']] = $title_info;
                }
            }

            //整理数据
            foreach ($topic_title_map as $topic_id => $title_id) {
                $list = $recommend_topics_map[$topic_id];
                $list['follow_count'] = &$follow_info_map[$topic_id];
                $list['new_content'] = $title_info_maps[$title_id];
                $result[] = $list;
            }
            unset($recommend_topics, $title_info_list, $topic_title_map, $recommend_topics_map, $title_info_maps);
        }

        return $result;
    }

    /**
     * 获取跟话题
     *
     * @param bool $map 根话题id和名字的对应关系
     * @param bool $as_recommend 是否仅获取推荐分类
     * @return mixed
     */
    function getRootTopics($map = false, $as_recommend = true)
    {
        $condition['parent_id'] = 0;
        if ($as_recommend) {
            $condition['as_recommend'] = 1;
        }

        if ($map) {
            return $this->link->select('topic_id, topic_name')->from($this->topic)
                ->where($condition)->stmt()->fetchAll(\PDO::FETCH_KEY_PAIR);
        } else {
            return $this->link->getAll($this->topic, 'topic_id, topic_name, topic_url', $condition, 'sort DESC');
        }
    }

    /**
     * 按分页获取子话题
     *
     * @param int $parent_id
     * @param array $page
     * @return array
     */
    function findChildTopics($parent_id, array &$page = array())
    {
        return $this->link->find($this->topic, 'topic_id, topic_name, topic_image, topic_url, topic_description', array(
            'parent_id' => $parent_id
        ), 'sort DESC', $page);
    }

    /**
     * 子话题列表
     *
     * @param int $parent_id
     * @param string $fields
     * @return mixed
     */
    function listChildTopics($parent_id, $fields = '*')
    {
        return $this->link->getAll($this->topic, $fields, array(
            'parent_id' => $parent_id
        ), 'sort DESC');
    }

    /**
     * 搜索topic
     *
     * @param string $topic_name
     * @param array $page
     * @param string $fields
     * @return mixed
     */
    function findTopicByName($topic_name, & $page = array('p' => 1, 'limit' => 30), $fields = '*')
    {
        return $this->link->find($this->topic, $fields, array(
            'parent_id' => array('<>', 0),
            'topic_name' => array('like', "%{$topic_name}%")
        ), 'sort ASC', $page);
    }

    /**
     * 检测话题url
     *
     * @param string $topic_url
     * @param int $topic_id
     * @return bool
     */
    function checkTopicUrl($topic_url, $topic_id)
    {
        $info = $this->link->get($this->topic, 'topic_id', array('topic_url' => $topic_url));
        if ($info && $info['topic_id'] != $topic_id) {
            return true;
        }

        return false;
    }

    /**
     * 根据名称获取话题信息
     *
     * @param array $topic_names
     * @param string $fields
     * @return mixed
     */
    function getTopicByNames(array $topic_names, $fields = '*')
    {
        return $this->link->getAll($this->topic, $fields, array(
                'topic_name' => array('IN', $topic_names), 'parent_id' => array('<>', 0))
        );
    }

    /**
     * 用户已关注话题
     *
     * @param $uid
     * @param string $fields
     * @return mixed
     */
    function getUserFollowingTopic($uid, $fields = 'ft.topic_id, t.topic_url, t.topic_name')
    {
        $uid = (int)$uid;
        if ($uid != 0) {
            return $this->link->getAll("{$this->following_topic} ft LEFT JOIN {$this->topic} t ON ft.topic_id=t.topic_id",
                $fields, array('uid' => $uid));
        }

        return array();
    }

    /**
     * 用户是否关注某话题
     *
     * @param int $uid
     * @param int $topic_id
     * @return bool
     */
    function getFollowingInfo($uid, $topic_id)
    {
        $uid = (int)$uid;
        $topic_id = (int)$topic_id;

        if ($uid > 0) {
            $following = $this->link->select('id, topic_id')->from($this->following_topic)
                ->where("uid={$uid} AND topic_id={$topic_id}")->getSQL(true);
            return $this->link->select('f.id is_following, count(ft.id) following_count')
                ->from("({$following}) f LEFT JOIN {$this->following_topic} ft ON f.topic_id=ft.topic_id")
                ->where(array('ft.topic_id' => $topic_id))->stmt()->fetch(\PDO::FETCH_ASSOC);
        } else {
            $following_info = $this->link->get($this->following_topic, 'count(*) count', array(
                'topic_id' => $topic_id
            ));

            return array(
                'is_following' => false,
                'following_count' => $following_info['count']
            );
        }
    }

    /**
     * 话题树形菜单
     *
     * @param array $condition
     * @return array
     */
    function getTopicTree($condition = array())
    {
        $topics = $this->link->getAll($this->topic, '*', $condition, 'sort ASC');
        $tree = new Tree();
        $tree->setTree($topics, 'topic_id', 'parent_id', '');
        return $tree->getArrayList();
    }

    /**
     * 话题信息
     *
     * @param string $topic_url
     * @return mixed
     */
    function getTopicInfoByUrl($topic_url)
    {
        return $this->link->get("{$this->topic} t LEFT JOIN {$this->topic} tt ON t.parent_id=tt.topic_id",
            't.*, tt.topic_name parent_topic_name, tt.topic_url parent_topic_url', array('t.topic_url' => $topic_url));
    }

    /**
     * 获取话题信息
     *
     * @param int $topic_id
     * @param string $fields
     * @return mixed
     */
    function getTopicInfo($topic_id, $fields = '*')
    {
        return $this->link->get($this->topic, $fields, array('topic_id' => (int)$topic_id));
    }

    /**
     * 删除话题
     *
     * @param int $topic_id
     * @return bool
     * @throws \Cross\Exception\CoreException
     */
    function delTopic($topic_id)
    {
        return $this->link->del($this->topic, array('topic_id' => (int)$topic_id));
    }

    /**
     * 获取相关话题
     *
     * @param int $topic_parent_id
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function getRelatedTopics($topic_parent_id)
    {
        return $this->link->select('topic_id, topic_name, topic_url')
            ->from($this->topic)
            ->where("parent_id = {$topic_parent_id}")
            ->orderBy('sort DESC')
            ->limit(10)
            ->stmt()->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 查询多个topic信息, id用逗号分隔
     *
     * @param string $topic_ids_string
     * @param string $fields
     * @return mixed
     */
    function getTopicInfo4Strings($topic_ids_string, $fields = '*')
    {
        if (!empty($topic_ids_string)) {
            return $this->link->getAll($this->topic, $fields, "topic_id IN({$topic_ids_string})");
        }

        return array();
    }

    /**
     * 批量获取话题信息
     *
     * @param array $topic_ids
     * @param bool $map
     * @param string $fields
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function getTopicsInfo(array $topic_ids, $map = false, $fields = '*')
    {
        if (!empty($topic_ids)) {
            $topics = $this->link->select($fields)->from($this->topic)
                ->where(array('topic_id' => array(
                    'IN', $topic_ids
                )))->stmt()->fetchAll(\PDO::FETCH_ASSOC);

            if ($map && !empty($topics)) {
                $topics_map = array();
                array_map(function ($topic) use (&$topics_map) {
                    $topics_map[$topic['topic_id']] = $topic;
                }, $topics);

                return $topics_map;
            }

            return $topics;
        }

        return array();
    }

    /**
     * 话题id和名称对应的map
     *
     * @return array|null
     * @throws \Cross\Exception\CoreException
     */
    function getTopicNameMap()
    {
        static $result = null;
        if ($result === null) {
            $result = $this->link->select('topic_id, topic_name')
                ->from($this->topic)->stmt()->fetchAll(\PDO::FETCH_KEY_PAIR);
        }

        return $result;
    }

    /**
     * 获取话题编辑
     *
     * @param int $topic_id
     * @return mixed
     */
    function getTopicEditor($topic_id)
    {
        return $this->link->get($this->topic_editor, '*', array('topic_id' => (int)$topic_id));
    }

    /**
     * 获取话题主编列表
     *
     * @param string $fields
     * @return mixed
     */
    function getChiefEditor($fields = '*')
    {
        return $this->link->get($this->topic_editor, $fields, array('topic_id' => 0));
    }

    /**
     * 保存主编数据
     *
     * @param string $editor_uid
     * @return bool|mixed
     * @throws \Cross\Exception\CoreException
     */
    function saveChiefEditor($editor_uid)
    {
        $isHave = $this->getChiefEditor(1);
        $data = array('editor_uid' => $editor_uid);
        if ($isHave) {
            return $this->link->update($this->topic_editor, $data, array(
                'topic_id' => 0
            ));
        } else {
            $data['topic_id'] = 0;
            return $this->link->add($this->topic_editor, $data);
        }
    }

    /**
     * 话题总编
     *
     * @return mixed
     */
    function getTopicChiefEditor()
    {
        return $this->link->get($this->topic_editor, '*', array('topic_id' => 0));
    }

    /**
     * 设置话题编辑
     *
     * @param int $topic_id
     * @param string $editor_uid
     * @return bool
     */
    function setTopicEditor($topic_id, $editor_uid)
    {
        $editor_uid = str_replace('，', ',', trim(trim($editor_uid, ',')));
        $isSetEditor = $this->getTopicEditor($topic_id);
        if ($isSetEditor) {
            return $this->link->update($this->topic_editor, array('editor_uid' => $editor_uid), array(
                'topic_id' => (int)$topic_id
            ));
        } else {
            return $this->link->add($this->topic_editor, array(
                'topic_id' => (int)$topic_id,
                'editor_uid' => $editor_uid
            ));
        }
    }

    /**
     * 批量更新话题信息
     *
     * @param array $data
     * @return bool
     */
    function updateTopicsInfo($data)
    {
        if (empty($data)) {
            return true;
        }

        foreach ($data as $topic_id => &$d) {
            self::getCheckBoxValue($d, 'as_recommend');
            self::getCheckBoxValue($d, 'enable_question');
            self::getCheckBoxValue($d, 'enable_posts');
            self::getCheckBoxValue($d, 'enable_article');
            $d['sort'] = (int)$d['sort'];
            $this->link->update($this->topic, $d, array('topic_id' => (int)$topic_id));
        }

        return true;
    }

    /**
     * 更新话题
     *
     * @param int $topic_id
     * @param array $data
     * @return bool
     */
    function updateTopicInfo($topic_id, $data)
    {
        self::getCheckBoxValue($data, 'as_recommend');
        self::getCheckBoxValue($data, 'enable_question');
        self::getCheckBoxValue($data, 'enable_posts');
        self::getCheckBoxValue($data, 'enable_article');

        return $this->link->update($this->topic, $data, array('topic_id' => (int)$topic_id));
    }

    /**
     * 添加话题
     *
     * @param array $data
     * @return bool|mixed
     * @throws \Cross\Exception\CoreException
     */
    function addTopic($data)
    {
        if (isset($data['topic_id'])) {
            unset($data['topic_id']);
        }

        self::getCheckBoxValue($data, 'as_recommend');
        self::getCheckBoxValue($data, 'enable_question');
        self::getCheckBoxValue($data, 'enable_posts');
        self::getCheckBoxValue($data, 'enable_article');
        $data['create_time'] = TIME;

        return $this->link->add($this->topic, $data);
    }

    /**
     * 获取多选输出的值
     *
     * @param array $data
     * @param string $key
     */
    private function getCheckBoxValue(&$data, $key)
    {
        if (!empty($data[$key]) && $data[$key] == 'on') {
            $data[$key] = 1;
        } else {
            $data[$key] = 0;
        }
    }
}
