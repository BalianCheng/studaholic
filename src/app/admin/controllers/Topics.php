<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Topic.php
 */

namespace app\admin\controllers;

use app\forum\modules\following\FollowingModule;
use app\forum\modules\title\TitleModule;
use app\forum\modules\topic\TopicModule;
use Cross\Core\Helper;
use lib\Images\UploadImages;

/**
 * 话题管理
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Topic
 * @package app\admin\controllers
 */
class Topics extends Forum
{
    /**
     * @cp_params parent_id, p=1
     */
    function index()
    {
        $parent_id = (int)$this->params['parent_id'];
        $TM = new TopicModule();
        $rootTopics = $TM->getRootTopics(false, false);
        if (empty($parent_id)) {
            if (!empty($rootTopics)) {
                $parent_id = $rootTopics[0]['topic_id'];
            }
        }

        $rootTopicsMap = array();
        foreach ($rootTopics as $topic) {
            $rootTopicsMap[$topic['topic_id']] = true;
        }

        if ($this->is_post()) {
            $TM->updateTopicsInfo($_POST);
            $this->to('topics:index', array('parent_id' => $parent_id));
        }

        $currentTopics = $TM->listChildTopics($parent_id);
        $this->data['parent_id'] = $parent_id;
        $this->data['rootTopics'] = $rootTopics;
        $this->data['currentTopics'] = $currentTopics;
        $this->display($this->data);
    }

    /**
     * 话题主编管理
     */
    function chiefEditor()
    {
        $TM = new TopicModule();
        if ($this->is_post() && !empty($_POST['editor_uid'])) {
            $editor_uid = trim(str_replace('，', ',', $_POST['editor_uid']));
            $editor_uid = array_unique(array_filter(explode(',', $editor_uid)));
            $editor_uid = implode(',', $editor_uid);
            $TM->saveChiefEditor($editor_uid);
            $this->to('topics:chiefEditor');
        }

        $chiefEditor = $TM->getChiefEditor();
        $this->data['chiefEditor'] = $chiefEditor;
        $this->display($this->data);
    }

    /**
     * 话题管理员设置UI
     */
    function managerUI()
    {
        $topic_id = &$_POST['topic_id'];

        $TM = new TopicModule();
        if (!empty($topic_id)) {
            $editor = $TM->getTopicEditor($topic_id);

            $this->data['editor'] = $editor;
            $this->data['topic_id'] = $topic_id;
            if ($this->is_ajax_request()) {
                $this->view->managerUI($this->data);
            } else {
                $this->display($this->data);
            }
        }
    }

    /**
     * 保存话题编辑
     */
    function saveManager()
    {
        $topic_id = &$_POST['topic_id'];
        $topic_editor_uid = &$_POST['editor_list'];
        if ($topic_id) {
            $TM = new TopicModule();
            $ret = $TM->setTopicEditor($topic_id, $topic_editor_uid);
            if (!$ret) {
                $this->data['status'] = 0;
            }
        }

        $this->display($this->data, 'JSON');
    }

    /**
     * 保存话题UI
     */
    function saveTopicUI()
    {
        $id = &$_POST['id'];
        $pid = &$_POST['pid'];

        $TM = new TopicModule();
        if (!empty($id)) {
            $topic = $TM->getTopicInfo($id);
            $pid = $topic['parent_id'];
            $this->data['topic'] = $topic;
        }

        $rootTopics = $TM->getRootTopics(true, false);

        $this->data['id'] = $id;
        $this->data['pid'] = $pid;
        $this->data['root'] = $rootTopics;
        if ($this->is_ajax_request()) {
            $this->view->saveTopicUI($this->data);
        } else {
            $this->display($this->data);
        }
    }

    /**
     * 检测话题url
     */
    function checkTopicUrl()
    {
        $TM = new TopicModule();
        $topic_id = &$_POST['topic_id'];
        $topic_url = &$_POST['topic_url'];
        $ret = $TM->checkTopicUrl($topic_url, (int)$topic_id);
        if ($ret) {
            $this->data['isHave'] = 1;
        } else {
            $this->data['isHave'] = 0;
        }

        $this->display($this->data, 'JSON');
    }

    /**
     * 保存话题
     */
    function saveTopic()
    {
        if ($this->is_post()) {
            if (!empty($_FILES)) {
                $frontendStaticPath = dirname(PROJECT_REAL_PATH) . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR;
                $fileDir = 'images/topics/upload';
                $filePath = $frontendStaticPath . $fileDir;
                Helper::createFolders($filePath);

                $imgName = date('Ymd_') . Helper::random(5);
                $IM = new UploadImages('topic_image', $imgName);
                $IM->setSavePath($filePath);
                $upload_info = $IM->thumb('96x96', false, true);

                if ($upload_info['status'] == 'ok') {
                    $_POST['topic_image'] = $fileDir . '/' . $upload_info['message']['thumb'][0];
                }
            }

            $TM = new TopicModule();
            $parent_id = $_POST['parent_id'];
            if (!empty($_POST['topic_id'])) {
                $topic_id = (int)$_POST['topic_id'];
                $TM->updateTopicInfo($topic_id, $_POST);
            } else {
                $TM->addTopic($_POST);
            }

            $this->to('topics:index', array('parent_id' => $parent_id));
        }

        $this->to('topics');
    }

    /**
     * 编辑根话题UI
     */
    function saveRootTopicUI()
    {
        $topic_id = &$_POST['id'];
        $this->data['topic'] = array();
        if ($topic_id) {
            $TM = new TopicModule();
            $this->data['topic'] = $TM->getTopicInfo($topic_id);
        }

        if ($this->is_ajax_request()) {
            $this->view->saveRootTopicUI($this->data);
        } else {
            $this->display($this->data);
        }
    }

    /**
     * 保存根话题
     */
    function saveRootTopic()
    {
        if ($this->is_post()) {
            $TM = new TopicModule();
            if (!empty($_POST['topic_id'])) {
                $topic_id = $_POST['topic_id'];
                $TM->updateTopicInfo($topic_id, $_POST);
            } else {
                unset($_POST['topic_id']);
                $_POST['parent_id'] = 0;
                $topic_id = $TM->addTopic($_POST);
            }

            $this->to('topics:index', array('parent_id' => $topic_id));
        }
        $this->to();
    }

    /**
     * 删除话题
     *
     * @params topic_id
     */
    function delTopic()
    {
        if ($this->is_post() && !empty($_POST['topic_id'])) {
            $TM = new TopicModule();
            $FM = new FollowingModule();
            $TTM = new TitleModule();

            $topic_id = (int)$_POST['topic_id'];
            $unDelTopic = $deletedTopic = array();
            $children_topic_list = $TM->listChildTopics($topic_id, 'topic_id, topic_name');
            if (!empty($children_topic_list)) {
                foreach ($children_topic_list as $child_topic) {
                    $child_topic_id = &$child_topic['topic_id'];
                    $title_count = $TTM->countTitleByTopicID($child_topic_id);
                    $follow_count = $FM->getTopicFollowingCount($child_topic_id);
                    if ($title_count == 0 && $follow_count == 0) {
                        $deletedTopic[] = $child_topic['topic_name'];
                        $TM->delTopic($child_topic_id);
                    } else {
                        $unDelTopic[] = $child_topic['topic_name'];
                    }
                }
            }

            $topic_info = $TM->getTopicInfo($topic_id, 'topic_id');
            if ($topic_info) {
                $title_count = $TTM->countTitleByTopicID($topic_id);
                $follow_count = $FM->getTopicFollowingCount($topic_id);
                if ($title_count == 0 && $follow_count == 0) {
                    $TM->delTopic($topic_id);
                } else {
                    $this->data['status'] = 0;
                    $this->data['message'] = "删除话题失败, 有{$title_count}条内容, {$follow_count}人关注";
                }
            }

            $this->data['delete'] = implode(',', $deletedTopic);
            $this->data['un_delete'] = implode(',', $unDelTopic);
            $this->display($this->data, 'JSON');
        } elseif ($this->is_ajax_request()) {
            $this->data['status'] = 0;
            $this->display($this->data, 'JSON');
        } else {
            $this->to('topics');
        }
    }
}

