<?php
namespace app\forum\modules\title;

use app\forum\modules\common\BaseModule;
use app\forum\modules\topic\TopicModule;

/**
 * @Auth: cmz <393418737@qq.com>
 * TitleModule.php
 */
class TitleModule extends BaseModule
{
    /**
     * 获取标题基本信息
     *
     * @param int $title_id
     * @param string $fields
     * @return mixed
     */
    function getTitleInfo($title_id, $fields = '*')
    {
        $title_id = (int)$title_id;
        return $this->link->get($this->title, $fields, array('title_id' => $title_id));
    }

    /**
     * 屏蔽主题
     *
     * @param int $title_id
     * @return bool
     */
    function blockTitle($title_id)
    {
        return $this->updateTitleInfo((int)$title_id, array('status' => 0));
    }

    /**
     * 取消主题屏蔽
     *
     * @param int $title_id
     * @return bool
     */
    function unBlockTitle($title_id)
    {
        return $this->updateTitleInfo((int)$title_id, array('status' => 1));
    }

    /**
     * 获取详细内容
     *
     * @param int $title_id
     * @return array|mixed
     * @throws \Cross\Exception\CoreException
     */
    function getTitleDetailInfo($title_id)
    {
        $fields = 't.title_id, t.title, t.type, t.interact_count, t.post_time, t.uid, t.up_count, t.status,
                u.account, u.nickname, u.avatar, u.introduce,
                p.posts_id,
                q.question_id, q.question_content,
                ar.article_id, ar.summary article_summary';

        $query = $this->link->select($fields)->from("{$this->title} t
            LEFT JOIN {$this->questions} q ON t.type=1 AND t.title_id=q.title_id
            LEFT JOIN {$this->posts} p ON t.type=2 AND t.title_id=p.title_id
            LEFT JOIN {$this->articles} ar ON t.type = 3 AND t.title_id = ar.title_id
            LEFT JOIN {$this->user} u ON u.uid=t.uid"
        )->where(array('t.title_id' => $title_id));

        $baseInfo = $query->stmt()->fetch(\PDO::FETCH_ASSOC);
        if (empty($baseInfo)) {
            return array();
        }

        switch ($baseInfo['type']) {
            case BaseModule::TYPE_ARTICLE:
                $content = $this->link->getAll($this->articles_content, 'id, content', array('article_id' => $baseInfo['article_id']));
                break;

            case BaseModule::TYPE_POSTS:
                $content = $this->link->getAll($this->posts_content, 'id, content', array('posts_id' => $baseInfo['posts_id']));
                break;

            case BaseModule::TYPE_QUESTION:
                $content[] = array(
                    'id' => $baseInfo['question_id'],
                    'content' => $baseInfo['question_content']
                );
                break;

            default:
                $content = array();
        }

        $baseInfo['content_list'] = $content;
        return $baseInfo;
    }

    /**
     * 根据title_id获取内容详情
     *
     * @param int $title_id
     * @return array
     */
    function getTitleSimpleDetailInfo($title_id)
    {
        $title_id = (int)$title_id;
        $query = $this->link->select('title_id')->from($this->title)->where("title_id = {$title_id}")->getSQL(true);
        return $this->simpleContentListDetail($query, true);
    }

    /**
     * 标题列表
     *
     * @param array $page
     * @param array $condition
     * @return mixed
     */
    function titleList(&$page = array(), $condition = array())
    {
        return $this->link->find($this->title, '*', $condition, 'title_id DESC', $page);
    }

    /**
     * 首页动态
     *
     * @param int $type
     * @param array $page
     * @param string $order
     * @param string $asc
     * @return array
     */
    function contentList($type = 0, & $page = array(), $order = 'time', $asc = 'DESC')
    {
        $condition = 'status=1';
        if ($type != 0 && isset(self::$typeMap[$type])) {
            $condition = "status=1 AND type={$type}";
        }

        $total = $this->link->get($this->title, 'count(1) total_result', $condition);
        $page['result_count'] = $total['total_result'];

        $page['limit'] = max(1, (int)$page['limit']);
        $page['total_page'] = ceil($page['result_count'] / $page['limit']);

        $list = array();
        if ($page['p'] <= $page['total_page']) {

            $order_config = array(
                'time' => 'title_id',
                'interact' => 'interact_count'
            );

            if (isset($order_config[$order])) {
                $order = $order_config[$order];
            } else {
                $order = $order_config['time'];
            }

            $page['p'] = max(1, $page['p']);
            $start = ($page['p'] - 1) * $page['limit'];

            $listSQL = $this->link->select('title_id')
                ->from($this->title)
                ->where($condition)
                ->orderBy(sprintf('%s %s', $order, $asc))
                ->limit($start, $page['limit'])->getSQL(true);

            $list = $this->contentListDetail($listSQL);
            if (count($list) < $page['limit']) {
                $list = $this->manualOrder($list, $order, $asc);
            }
        }

        return $list;
    }

    /**
     * 主题统计
     *
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function getContentCount()
    {
        return $this->link->select('type, count(1) count')->from($this->title)->groupBy('type')
            ->stmt()->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * 内容数量统计
     *
     * @param int $start_time
     * @param int $end_time
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function getContentNum($start_time, $end_time)
    {
        return $this->link->select('type, count(1) count')->from($this->title)->where(array(
            'post_time > ? AND post_time < ?', array($start_time, $end_time)
        ))->groupBy('type')->stmt()->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * 话题动态
     *
     * @param int $topic_id
     * @param int $type
     * @param array $page
     * @param string $order
     * @param string $asc
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function topicContentList($topic_id, $type, &$page = array(), $order = 'time', $asc = 'DESC')
    {
        $topic_title_info = $this->link->get($this->topic_title_id, 'count(1) total', array('topic_id' => $topic_id, 'type' => $type));
        $page['result_count'] = $topic_title_info['total'];
        $page['limit'] = max(1, (int)$page['limit']);
        $page['total_page'] = ceil($page['result_count'] / $page['limit']);

        $list = array();
        if ($page['p'] <= $page['total_page']) {

            $order_config = array(
                'time' => 'title_id',
                'interact' => 'interact_count'
            );

            if (isset($order_config[$order])) {
                $order = $order_config[$order];
            } else {
                $order = $order_config['time'];
            }

            $page['p'] = max(1, $page['p']);
            $start = ($page['p'] - 1) * $page['limit'];

            $content_sql = $this->link->select('t.title_id')
                ->from("{$this->title} t JOIN {$this->topic_title_id} tti ON t.title_id=tti.title_id AND t.type={$type}")
                ->where("t.status=1 AND tti.topic_id={$topic_id}")
                ->orderBy(sprintf('t.%s %s', $order, $asc))
                ->limit($start, $page['limit'])->getSQL(true);

            $list = $this->contentListDetail($content_sql);
            if (count($list) < $page['limit']) {
                $list = $this->manualOrder($list, $order, $asc);
            }
        }

        return $list;
    }

    /**
     * 增加互动计数
     *
     * @param int $title_id
     * @throws \Cross\Exception\CoreException
     */
    function addInteractCount($title_id)
    {
        $this->link->update($this->title, 'interact_count=interact_count+1, last_interact_time=' . TIME, array('title_id' => $title_id));
    }

    /**
     * 减少互动计数
     *
     * @param int $title_id
     */
    function minusInteractCount($title_id)
    {
        $this->link->update($this->title, 'interact_count=interact_count-1', array('title_id' => $title_id));
    }

    /**
     * 被赞次数加一
     *
     * @param int $title_id
     */
    function addUpCount($title_id)
    {
        $this->link->update($this->title, 'up_count=up_count+1', array('title_id' => $title_id));
    }

    /**
     * 被赞次数减一
     *
     * @param int $title_id
     */
    function minusUpCount($title_id)
    {
        $this->link->update($this->title, 'up_count=up_count-1', array('title_id' => $title_id));
    }

    /**
     * 推荐内容列表
     *
     * @return array|mixed
     */
    function getRecommendContentList()
    {
        $allContentInfo = array();
        $recommend_list = $this->link->getAll($this->recommend_title, '*', array(), 'sort DESC, ct DESC');

        if (!empty($recommend_list)) {
            $title_id = $title_id_map = array();
            foreach ($recommend_list as $recommend) {
                $title_id[] = $recommend['title_id'];
                $title_id_map[$recommend['title_id']] = $recommend;
            }

            $order = 'field(title_id,' . implode(',', $title_id) . ')';
            $allContentInfo = $this->link->getAll($this->title, '*', array('title_id' => array('IN', $title_id)), $order);
            foreach ($allContentInfo as &$content) {
                $recommendInfo = &$title_id_map[$content['title_id']];
                $content['recommend_id'] = $recommendInfo['id'];
                $content['recommend_sort'] = $recommendInfo['sort'];
                $content['recommend_time'] = $recommendInfo['ct'];
            }
        }

        return $allContentInfo;
    }

    /**
     * 批量更新推荐排序
     *
     * @param array $update_data
     * @return bool
     */
    function updateRecommendContentOrder($update_data)
    {
        foreach ($update_data as $id => $data) {
            $this->link->update($this->recommend_title, array(
                '`sort`' => (int)$data['sort']
            ), array('id' => (int)$id));
        }
        return true;
    }

    /**
     * 编辑推荐
     *
     * @param int $count
     * @param string $order
     * @param string $asc
     * @return array
     */
    function editorRecommendContentList($count = 5, $order = 'sort', $asc = 'DESC')
    {
        $recommend_sql = $this->link->select('title_id')->from($this->recommend_title)
            ->orderBy(sprintf('%s %s, ct DESC', $order, $asc))->limit($count)->getSQL(true);
        return $this->contentListDetail($recommend_sql);
    }

    /**
     * 编辑推荐title_id和推荐id对应关系
     *
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function editorRecommendMap()
    {
        return $this->link->select('title_id, id')->from($this->recommend_title)
            ->stmt()->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * 增加编辑推荐
     *
     * @param int $title_id
     * @return bool|mixed
     * @throws \Cross\Exception\CoreException
     */
    function addEditorRecommend($title_id)
    {
        return $this->link->add($this->recommend_title, array(
            'title_id' => $title_id,
            'sort' => 1,
            'ct' => TIME
        ));
    }

    /**
     * 删除编辑推荐
     *
     * @param int $id
     * @return bool
     * @throws \Cross\Exception\CoreException
     */
    function delEditorRecommend($id)
    {
        return $this->link->del($this->recommend_title, array('id' => (int)$id));
    }

    /**
     * 获取用户发布的主题列表
     *
     * @param int $uid
     * @param int $type
     * @param array $page
     * @return mixed
     */
    function findUserContentList($uid, $type, array &$page)
    {
        $uid = (int)$uid;
        $type = (int)$type;
        $title_info = $this->link->get($this->title, 'count(1) total', array('uid' => $uid, 'type' => $type));

        $page['result_count'] = $title_info['total'];

        $page['limit'] = max(1, (int)$page['limit']);
        $page['total_page'] = ceil($page['result_count'] / $page['limit']);

        $result = array();
        if ($page['p'] <= $page['total_page']) {

            $page['p'] = max(1, $page['p']);
            $start = ($page['p'] - 1) * $page['limit'];

            $content_sql = $this->link->select('title_id')->from($this->title)
                ->where("uid={$uid} AND type={$type} AND status=1")->orderBy('title_id DESC')
                ->limit($start, $page['limit'])->getSQL(true);

            $result = $this->contentListDetail($content_sql);
        }

        return $result;
    }

    /**
     * 主题及数据简单对齐
     *
     * @param string $content_sql
     * @param bool $getOne
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function simpleContentListDetail($content_sql, $getOne = false)
    {
        $fields = 't.title_id, t.title, t.type, t.interact_count, t.post_time, t.uid, t.up_count, t.status,
                u.account, u.nickname, u.avatar, u.introduce, p.posts_id, q.question_id, ar.article_id';

        $query = $this->link->select($fields)->from("({$content_sql}) content
            LEFT JOIN {$this->title} t ON t.title_id=content.title_id
            LEFT JOIN {$this->questions} q ON t.type=1 AND t.title_id=q.title_id
            LEFT JOIN {$this->posts} p ON t.type=2 AND t.title_id=p.title_id
            LEFT JOIN {$this->articles} ar ON t.type = 3 AND t.title_id = ar.title_id
            LEFT JOIN {$this->user} u ON u.uid=t.uid"
        );

        if ($getOne) {
            return $query->stmt()->fetch(\PDO::FETCH_ASSOC);
        }

        return $query->stmt()->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 动态数据结果对齐
     *
     * @param $content_sql
     * @param string $content_addition_fields
     * @param array $title_ids
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function contentListDetail($content_sql, $content_addition_fields = '', &$title_ids = array())
    {
        $fields = 't.title_id, t.title, t.type, t.topic_ids, t.interact_count, t.post_time, t.uid, t.up_count, t.status, t.last_interact_time,
                u.account, u.nickname, u.avatar, u.introduce,
                uu.account answer_account, uu.nickname answer_nickname, uu.avatar answer_avatar, uu.introduce answer_introduce,
                p.posts_id, p.hits posts_hits, p.posts_status, LEFT(pc.content, 128) posts_content,
                q.question_id, q.hits question_hits, LEFT(q.question_content, 128) question_content,
                a.answer_id, LEFT(a.answer_content, 128) answer_content, a.uid answer_uid,
                qas.up_count answer_up_count,
                ar.article_id, ar.hits article_hits, ar.summary article_summary';

        if (!empty($content_addition_fields)) {
            $content_addition_fields = explode(',', $content_addition_fields);
            foreach ($content_addition_fields as &$addition_filed) {
                $addition_filed = trim($addition_filed);
                $addition_filed = "content.{$addition_filed} content_{$addition_filed}";
            }
            $fields .= ',' . implode(', ', $content_addition_fields);
        }

        $query = $this->link->select($fields)->from("({$content_sql}) content
            LEFT JOIN {$this->title} t ON t.title_id=content.title_id
            LEFT JOIN {$this->questions} q ON t.type=1 AND t.title_id=q.title_id
            LEFT JOIN {$this->posts} p ON t.type=2 AND t.title_id=p.title_id
            LEFT JOIN {$this->posts_content} pc ON p.posts_id=pc.posts_id AND p=1
            LEFT JOIN {$this->articles} ar ON t.type = 3 AND t.title_id = ar.title_id
            LEFT JOIN {$this->answers} a ON q.best_answer_id = a.answer_id
            LEFT JOIN {$this->answers_stat} qas on q.best_answer_id = qas.answer_id
            LEFT JOIN {$this->user} u ON u.uid=t.uid
            LEFT JOIN {$this->user} uu ON uu.uid=a.uid"
        );

        $topic_ids = $title_ids = array();
        $content_list = $query->stmt()->fetchAll(\PDO::FETCH_ASSOC);
        if (!empty($content_list)) {
            array_walk($content_list, function (&$list) use (&$title_ids, &$topic_ids) {
                $title_ids[] = $list['title_id'];

                if (!empty($list['topic_ids'])) {
                    $list_topic_ids = explode(',', $list['topic_ids']);
                    array_map(function ($topic_id) use (&$topic_ids) {
                        $topic_ids[$topic_id] = 1;
                    }, $list_topic_ids);

                    $list['topic_ids_array'] = $list_topic_ids;
                } else {
                    $list['topic_ids_array'] = array();
                }

                if (!empty($list['question_content'])) {
                    $list['question_content'] = preg_replace("~\x{00a0}~siu", '', strip_tags($list['question_content']));
                }

                if (!empty($list['posts_content'])) {
                    $list['posts_content'] = preg_replace("~\x{00a0}~siu", '', strip_tags($list['posts_content']));
                }

                if (!empty($list['answer_content'])) {
                    $list['answer_content'] = preg_replace("~\x{00a0}~siu", '', strip_tags($list['answer_content']));
                }
            });
        }

        $topic_ids = array_keys($topic_ids);
        $TM = new TopicModule();
        $topic_info = $TM->getTopicsInfo($topic_ids, true, 'topic_id, topic_name, topic_url, topic_image, topic_description');

        $title_images = $this->getTitlesImages($title_ids);
        foreach ($content_list as &$content) {

            //主题所属话题
            $content['topic_info'] = array();
            if (!empty($content['topic_ids_array'])) {
                foreach ($content['topic_ids_array'] as $topic_id) {
                    $content['topic_info'][] = &$topic_info[$topic_id];
                }
            }

            //主题包含的图片
            $content['images'] = array();
            if (isset($title_images[$content['title_id']])) {
                $content['images'] = $title_images[$content['title_id']];
            }
        }

        return $content_list;
    }

    /**
     * 列出所属话题的标题
     *
     * @param int $topic_id
     * @return mixed
     */
    function countTitleByTopicID($topic_id)
    {
        $info = $this->link->get($this->title, 'count(1) count', array('topic_ids' => $topic_id));
        if ($info) {
            return $info['count'];
        }

        return 0;
    }

    /**
     * 手动排序
     *
     * @param array $items
     * @param $key
     * @param string $asc
     * @return array
     */
    private function manualOrder(array $items, $key, $asc = 'DESC')
    {
        usort($items, function ($a, $b) use ($key, $asc) {
            if ($asc == 'DESC') {
                return $a[$key] < $b[$key];
            } else {
                return $a[$key] > $b[$key];
            }
        });
        return $items;
    }

    /**
     * 更新主题内容
     *
     * @param int $title_id
     * @param array $data
     * @return bool
     */
    private function updateTitleInfo($title_id, $data)
    {
        return $this->link->update($this->title, $data, array('title_id' => $title_id));
    }
}
