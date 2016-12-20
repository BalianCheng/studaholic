<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Topics.php
 */

namespace app\forum\controllers;

use app\forum\modules\following\FollowingModule;
use app\forum\modules\title\TitleModule;
use app\forum\modules\common\BaseModule;
use app\forum\modules\topic\TopicModule;

/**
 * 话题
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Topics
 * @package app\forum\controllers
 */
class Topics extends Forum
{
    /**
     * @var TopicModule
     */
    protected $TOPIC;

    /**
     * 类型名称和预设值的对应关系
     *
     * @var array
     */
    protected static $typeConfig = array(
        'question' => BaseModule::TYPE_QUESTION,
        'posts' => BaseModule::TYPE_POSTS,
        'article' => BaseModule::TYPE_ARTICLE,
    );

    function __construct()
    {
        parent::__construct();
        $this->TOPIC = new TopicModule();
    }

    /**
     * @return mixed
     *
     * @cp_params topic_url, p=1
     */
    function index()
    {
        $topic_url = $this->params['topic_url'];
        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 50,
            'half' => 10,
            'link' => array('topics:index', array('topic_url' => $topic_url))
        );

        $parent_topic_id = 0;
        $rootTopics = $this->TOPIC->getRootTopics(false, false);

        if (!empty($topic_url)) {
            foreach ($rootTopics as $t) {
                if ($t['topic_url'] == $topic_url) {
                    $parent_topic_id = $t['topic_id'];
                    break;
                }
            }
        } elseif (!empty($rootTopics[0])) {
            $topic_url = $rootTopics[0]['topic_url'];
            $parent_topic_id = $rootTopics[0]['topic_id'];
        }

        $topicList = $this->TOPIC->findChildTopics($parent_topic_id, $page);
        $user_following_topic = $this->TOPIC->getUserFollowingTopic($this->uid);

        $user_topic_map = array();
        array_map(function ($t) use (&$user_topic_map) {
            $user_topic_map[$t['topic_id']] = 1;
        }, $user_following_topic);

        //当前话题
        foreach ($topicList as &$t) {
            if (isset($user_topic_map[$t['topic_id']])) {
                $t['is_following'] = 1;
            } else {
                $t['is_following'] = 0;
            }
        }

        //推荐话题
        $recommend_topic = $this->TOPIC->getRecommendTopic('*', 5);
        foreach ($recommend_topic as &$t) {
            if (isset($user_topic_map[$t['topic_id']])) {
                $t['is_following'] = 1;
            } else {
                $t['is_following'] = 0;
            }
        }

        $this->data['topic_url'] = $topic_url;
        $this->data['parent_topic_id'] = $parent_topic_id;
        $this->data['following_topic'] = $user_following_topic;
        $this->data['recommend_topic'] = $recommend_topic;
        $this->data['rootTopics'] = $rootTopics;
        $this->data['topicsList'] = $topicList;
        $this->data['page'] = $page;

        $this->display($this->data);
    }

    /**
     * 话题
     *
     * @cp_params topic_url, type, order=time, p=1
     */
    function detail()
    {
        $order = $this->params['order'];
        $type_name = $this->params['type'];
        $topic_url = $this->params['topic_url'];
        if (empty($topic_url)) {
            $this->to();
        }

        $TM = new TopicModule();
        $topic_info = $TM->getTopicInfoByUrl($topic_url);
        if (empty($topic_info)) {
            $this->to();
        }

        //判断是否是根话题
        if ($topic_info['parent_id'] == 0) {
            $this->to('topics:index', array('topic_url' => $topic_info['topic_url']));
        }

        $topic_public_status = array(
            'question' => $topic_info['enable_question'],
            'posts' => $topic_info['enable_posts'],
            'article' => $topic_info['enable_article']
        );

        if (empty($type_name)) {
            foreach ($topic_public_status as $type => $status) {
                if ($status == 1) {
                    $type_name = $type;
                    break;
                }
            }
        } elseif (empty($topic_public_status[$type_name])) {
            $this->to('topics:detail', array('topic_url' => $topic_url));
        }

        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 50,
            'half' => 5,
            'link' => array('topics:detail', array('topic_url' => $topic_url, 'type' => $type_name, 'order' => $order))
        );

        $TITLE = new TitleModule();
        if ($type_name) {
            $topic_content = $TITLE->topicContentList($topic_info['topic_id'], self::$typeConfig[$type_name], $page, $order);
        } else {
            $topic_content = array();
        }

        //相关话题
        $related_topics = $TM->getRelatedTopics($topic_info['parent_id']);

        //用户是否已关注和关注人数
        $TOPIC = new TopicModule();
        $follow_info = $TOPIC->getFollowingInfo($this->uid, $topic_info['topic_id']);

        $this->data['page'] = $page;
        $this->data['type_name'] = $type_name;
        $this->data['order'] = $order;
        $this->data['topic_url'] = $topic_url;
        $this->data['topic_info'] = $topic_info;
        $this->data['follow_info'] = $follow_info;
        $this->data['topic_public_status'] = $topic_public_status;
        $this->data['publish_add_topic_name'] = $topic_info['topic_name'];
        $this->data['content'] = $topic_content;
        $this->data['related_topics'] = $related_topics;

        $this->display($this->data);
    }

    /**
     * 关注话题
     *
     * @cp_params topic_url, topic_id
     */
    function following()
    {
        if (!$this->isLogin) {
            $this->loginAfterReturn();
        }

        $topic_url = $this->params['topic_url'];
        $topic_id = (int)$this->params['topic_id'];
        $FOLLOWING = new FollowingModule();
        $FOLLOWING->topic($this->uid, $topic_id);

        $referrer = $this->request->getUrlReferrer();
        if($referrer) {
            $this->redirect($referrer);
        } else {
            $this->to('topics:index', array('topic_url' => $topic_url));
        }
    }

    /**
     * 取消关注的话题
     *
     * @cp_params topic_url, topic_id
     */
    function unFollowing()
    {
        if (!$this->isLogin) {
            $this->loginAfterReturn();
        }

        $topic_url = $this->params['topic_url'];
        $topic_id = (int)$this->params['topic_id'];
        $FOLLOWING = new FollowingModule();
        $FOLLOWING->unFollowingTopic($this->uid, $topic_id);

        $referrer = $this->request->getUrlReferrer();
        if($referrer) {
            $this->redirect($referrer);
        } else {
            $this->to('topics:index', array('topic_url' => $topic_url));
        }
    }
}
