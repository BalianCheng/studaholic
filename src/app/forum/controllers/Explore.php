<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Explore.php
 */

namespace app\forum\controllers;

use app\forum\modules\common\RecommendModule;
use app\forum\modules\title\TitleModule;
use app\forum\modules\topic\TopicModule;

/**
 * 发现
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Explore
 * @package app\forum\controllers
 */
class Explore extends Forum
{

    /**
     * 发现首页
     *
     * @cp_params order=time, p=1
     * @return mixed
     */
    function index()
    {
        $order = $this->params['order'];
        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 50,
            'half' => 5,
            'link' => array('explore', array('order' => $order))
        );

        $TITLE = new TitleModule();
        $mode = (int)$this->siteConfig->get('mode');
        $content_list = $TITLE->contentList($mode, $page, $order);

        $this->data['mode'] = $mode;
        $this->data['page'] = $page;
        $this->data['order'] = $order;
        $this->data['content_list'] = $content_list;

        if ($this->is_ajax_request()) {
            if ($mode == 0) {
                $fragment_type = 'explore/mixed';
            } else {
                $fragment_type = 'explore/single';
            }
            $this->view->contentListSection($content_list, $fragment_type);
        } else {
            $TOPIC = new TopicModule();
            $RECOMMEND = new RecommendModule();
            $this->data['recommend_user'] = $RECOMMEND->getSiteRecommendUser(5);
            $this->data['recommend_topic'] = $TOPIC->getRecommendTopicNewTopic(5);
            $this->data['recommend_content_list'] = $TITLE->editorRecommendContentList(10);
            $this->display($this->data);
        }
    }
}
