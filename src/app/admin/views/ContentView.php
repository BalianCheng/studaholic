<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * ContentView.php
 */

namespace app\admin\views;

use app\forum\modules\account\AccountModule;

/**
 * 内容管理视图控制器
 *
 * @Auth: cmz <393418737@qq.com>
 * Class ContentView
 * @package app\admin\views
 */
class ContentView extends ForumView
{
    /**
     * 内容列表
     *
     * @param $data
     */
    function index($data)
    {
        $view = &$this;
        $topic = &$data['topicList'];
        $recommendMap = &$data['recommendMap'];
        $typeNameConfig = &$data['typeNameConfig'];
        $user = new AccountModule();
        array_walk($data['list'], function (&$data) use ($view, $topic, $user, $typeNameConfig, $recommendMap) {
            $data['topic_names'] = '';
            if ($data['post_ip']) {
                $data['post_ip'] = long2ip($data['post_ip']);
            }

            if ($data['post_time']) {
                $data['post_time'] = date('Y-m-d H:i:s', $data['post_time']);
            }

            $data['author_name'] = $user->getUserNickname($data['uid']);
            $data['user_filter_link'] = $view->url('content:index', array('filter_type' => 'user', 'filter_id' => $data['uid']));
            $data['type_filter_link'] = $view->url('content:index', array('filter_type' => 'type', 'filter_id' => $data['type']));

            $data['recommend_id'] = 0;
            if (isset($recommendMap[$data['title_id']])) {
                $data['recommend_id'] = $recommendMap[$data['title_id']];
            }

            if (isset($typeNameConfig[$data['type']])) {
                $typeName = $typeNameConfig[$data['type']];
            } else {
                $typeName = '未知类型';
            }

            $data['type_name'] = $typeName;
            $topic_ids = explode(',', $data['topic_ids']);
            if (!empty($topic_ids)) {
                foreach ($topic_ids as $topic_id) {
                    if (isset($topic[$topic_id])) {
                        $filter_url = $view->url('content:index', array('filter_type' => 'topic', 'filter_id' => $topic_id));
                        $data['topic_names'] .= $view->a($topic[$topic_id], $filter_url) . ' ';
                    }
                }
            }
        });

        $this->renderTpl('content/list', $data);
    }

    /**
     * 推荐内容列表
     *
     * @param array $data
     */
    function recommendList($data=array())
    {
        $this->renderTpl('content/recommend_list', $data);
    }

    /**
     * 屏蔽UI
     *
     * @param array $data
     */
    function blockContentUI($data = array())
    {
        $this->renderTpl('content/block', $data);
    }

    /**
     * 内容预览
     *
     * @param $data
     */
    function preview($data)
    {
        $this->renderTpl('content/detail', $data);
    }
}
