<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Content.php
 */

namespace app\admin\controllers;

use app\forum\modules\account\AccountModule;
use app\forum\modules\common\BaseModule;
use app\forum\modules\message\MessageModule;
use app\forum\modules\title\TitleModule;
use app\forum\modules\topic\TopicModule;
use DOMDocument;

/**
 * 内容管理
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Content
 * @package app\admin\controllers
 */
class Content extends Forum
{
    /**
     * @cp_params filter_type, filter_id, key, p=1
     */
    function index()
    {
        $page = array(
            'p' => $this->params['p'],
            'limit' => 30,
            'link' => array('content:index', array_filter($this->params))
        );

        $filter_id = (int)$this->params['filter_id'];
        $filter_type = $this->params['filter_type'];

        $typeNameConfig = array(
            BaseModule::TYPE_QUESTION => '问题',
            BaseModule::TYPE_ARTICLE => '文章',
            BaseModule::TYPE_POSTS => '帖子',
        );

        $searchOptionConfig = array(1 => '标题', 2 => 'id');

        $TOPIC = new TopicModule();
        $topicList = $TOPIC->getTopicNameMap();

        switch ($filter_type) {
            case 'user':
                $user = new AccountModule();
                $nickname = $user->getUserNickname($filter_id);
                if (!$nickname) {
                    $this->to('content');
                }

                $tips = "用户 {$nickname} 发布的内容列表";
                $condition = array('uid' => $filter_id);
                break;

            case 'type':
                if (!isset($typeNameConfig[$filter_id])) {
                    $this->to('content');
                }
                $tips = "所有{$typeNameConfig[$filter_id]}列表";
                $condition = array('type' => $filter_id);
                break;

            case 'topic':
                if (!isset($topicList[$filter_id])) {
                    $this->to('content');
                }

                $topic_name = $topicList[$filter_id];
                $tips = "话题 {$topic_name} 内容列表";
                $condition = "topic_ids REGEXP '[[:<:]]({$filter_id})[[:>:]]'";
                break;

            case 'search':
                if (empty($this->params['key'])) {
                    $this->to('content');
                }

                $searchKey = $this->params['key'];
                if ($filter_id == 1) {
                    $tips = "包含关键词 {$searchKey} 的内容列表";
                    $condition = array('title' => array('like', "%{$searchKey}%"));
                } else {
                    $tips = "ID为 {$searchKey} 的内容";
                    $condition = array('title_id' => (int)$searchKey);
                }
                break;

            case 'status':
                $tips = '已屏蔽的内容列表';
                $condition = array('status' => 0);
                break;

            default:
                $tips = null;
                $condition = array('status' => 1);
        }

        $TM = new TitleModule();
        //内容列表
        $list = $TM->titleList($page, $condition);
        //编辑推荐
        $recommendMap = $TM->editorRecommendMap();

        $this->data['list'] = $list;
        $this->data['page'] = $page;
        $this->data['topicList'] = $topicList;
        $this->data['typeNameConfig'] = $typeNameConfig;
        $this->data['recommendMap'] = $recommendMap;
        $this->data['filterTips'] = $tips;
        $this->data['searchOption'] = $searchOptionConfig;
        $this->data['searchParams'] = 1;
        $this->data['addClass'] = 'sidebar-collapse';

        $this->display($this->data);
    }

    /**
     * @cp_params p
     */
    function recommendList()
    {
        $TM = new TitleModule();
        $list = $TM->getRecommendContentList();

        if($this->is_post()) {
            $TM->updateRecommendContentOrder($_POST);
            $this->to('content:recommendList');
        }

        $this->data['list'] = $list;
        $this->display($this->data);
    }

    /**
     * 内容屏蔽UI
     */
    function blockContentUI()
    {
        $title_id = &$_POST['title_id'];
        $TM = new TitleModule();
        $titleInfo = $TM->getTitleDetailInfo($title_id);
        if ($titleInfo) {
            $this->view->blockContentUI($titleInfo);
        } else {
            $this->view->modalError('内容不存在');
        }
    }

    /**
     * 内容屏蔽
     */
    function blockContent()
    {
        if (!$this->is_post()) {
            $this->to('content');
        }

        $TM = new TitleModule();
        $title_id = &$_POST['title_id'];
        $reason = '涉嫌违规';
        if (!empty($_POST['reason'])) {
            $reason = $_POST['reason'];
        }

        $titleInfo = $TM->getTitleDetailInfo($title_id);
        if ($titleInfo) {
            if ($titleInfo['status'] == 0) {
                $messageTpl = $this->loadConfig('message.config.php')->get('unblock');
                $messageContent = str_replace('{title}', $titleInfo['title'], $messageTpl);

                $type = 'unblock';
                $blockRet = $TM->unBlockTitle($titleInfo['title_id']);
            } else {
                $messageTpl = $this->loadConfig('message.config.php')->get('block');
                $messageContent = str_replace(array('{title}', '{reason}'), array($titleInfo['title'], nl2br($reason)), $messageTpl);

                $type = 'block';
                $blockRet = $TM->blockTitle($titleInfo['title_id']);
            }

            if ($blockRet) {
                $MM = new MessageModule();
                $MM->sendSysMessage($titleInfo['uid'], $messageContent);
            }
        } else {
            $type = 'error';
        }

        $this->data['type'] = $type;
        $this->display($this->data, 'JSON');
    }

    /**
     * 推荐/取消推荐
     */
    function recommend()
    {
        if ($this->is_post()) {

            $title_id = &$_POST['title_id'];
            $recommend_id = &$_POST['recommend_id'];

            $TM = new TitleModule();
            //取消推荐
            if ($recommend_id > 0 && $title_id > 0) {
                $type = 'cancel';
                $ret = (int)$TM->delEditorRecommend($recommend_id);
                $recommend_id = 0;
            } elseif ($recommend_id == 0 && $title_id > 0) {
                $type = 'recommend';
                $recommend_id = (int)$TM->addEditorRecommend($title_id);
                $ret = 1;
            } else {
                $type = 'error';
                $ret = 0;
            }

            $this->data['ret'] = $ret;
            $this->data['type'] = $type;
            $this->data['recommend_id'] = $recommend_id;
            $this->display($this->data, 'JSON');
        } else {
            $this->to();
        }
    }

    /**
     * 内容预览
     */
    function preview()
    {
        $title_id = &$_POST['title_id'];
        $title_id = (int)$title_id;

        $max_width = &$_POST['max_width'];
        if ($max_width > 768) {
            $image_width = 570;
        } else {
            $image_width = 270;
        }

        $TM = new TitleModule();
        $title_info = $TM->getTitleDetailInfo($title_id);
        if (!$title_info) {
            $this->view->alert("没找到该内容");
            return;
        }

        //处理图片地址
        $DOCUMENT = new DOMDocument();
        $contentList = &$title_info['content_list'];
        foreach ($contentList as &$content) {
            if (!empty($content['content'])) {
                @$DOCUMENT->loadHTML(mb_convert_encoding($content['content'], 'HTML-ENTITIES', 'UTF-8'));
                foreach ($DOCUMENT->getElementsByTagName('img') as $imgNode) {
                    if ($imgNode->hasAttribute('data-original')) {
                        $abs_url = $imgNode->getAttribute('data-original');
                        $imgNode->setAttribute('src', $abs_url);
                        $imgNode->setAttribute('style', "max-width:{$image_width}px;");
                    }
                }

                $content['content'] = $DOCUMENT->saveHTML($DOCUMENT->documentElement);
                $content['content'] = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $content['content']);
            }
        }

        $this->data['info'] = $title_info;
        $this->view->preview($this->data);
    }
}
