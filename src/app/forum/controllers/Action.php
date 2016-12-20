<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Api.php
 */

namespace app\forum\controllers;

use app\forum\modules\collection\CollectionModule;
use app\forum\modules\common\ReportModule;
use app\forum\modules\following\FollowingModule;
use app\forum\modules\common\RecommendModule;
use app\forum\modules\content\QuestionModule;
use app\forum\modules\content\ArticleModule;
use app\forum\modules\account\AccountModule;
use app\forum\modules\invite\InviteModule;
use app\forum\modules\content\PostsModule;
use app\forum\modules\message\MessageModule;
use app\forum\modules\topic\TopicModule;
use app\forum\views\ForumView;
use lib\Images\UploadImages;
use Cross\Core\Helper;

/**
 * 内部api
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Action
 * @package app\forum\controllers
 */
class Action extends Forum
{
    /**
     * 默认数据结构
     *
     * @var array
     */
    protected $data = array('status' => 0, 'message' => 'ok');

    /**
     * 获取话题
     *
     * @throws \Cross\Exception\CoreException
     */
    function findTopic()
    {
        if (!$this->is_ajax_request()) {
            $this->display($this->result(0));
        }

        $TOPIC = new TopicModule();
        $q = self::getEntitiesData(strip_tags($_POST['q']));

        if (isset($_POST['p'])) {
            $p = (int)$_POST['p'];
        } else {
            $p = 1;
        }

        $page = array(
            'p' => $p,
            'limit' => 30,
        );

        $result = $TOPIC->findTopicByName($q, $page, 'topic_id id, topic_name text, topic_image');

        $this->data['status'] = 1;
        $this->data['data'] = array(
            'items' => $result,
            'page' => $page
        );

        $this->display($this->data);
    }

    /**
     * 根据话题名称获取话题id
     */
    function findTopicByName()
    {
        if (!$this->is_ajax_request()) {
            $this->display($this->result(0));
        }

        $TOPIC = new TopicModule();
        $topics_name = explode(',', self::getEntitiesData(strip_tags($_POST['topics'])));
        $topics_array = array_map('urldecode', array_unique(array_slice($topics_name, 0, 5)));

        $type = null;
        $topic_ids = array();
        if (isset($_POST['type'])) {
            $type = self::getEntitiesData(strip_tags($_POST['type']));
        }

        $result = $TOPIC->getTopicByNames($topics_array);
        if (!empty($result)) {
            foreach ($result as $r) {
                $can_choose = 1;
                if ($type && $r["enable_{$type}"] != 1) {
                    $can_choose = 0;
                }

                $topic_ids[] = array(
                    'topic_id' => $r['topic_id'],
                    'topic_name' => $r['topic_name'],
                    'as_recommend' => $r['as_recommend'],
                    'can_choose' => $can_choose,
                );
            }
        }

        $this->data['status'] = 1;
        $this->data['data'] = $topic_ids;
        $this->display($this->data);
    }

    /**
     * 获取已关注话题
     */
    function getUserFollowingTopics()
    {
        $this->checkLoginStatus($uid, true);
        $TOPIC = new TopicModule();
        $following_topic = $TOPIC->getUserFollowingTopic($uid, 'ft.topic_id, t.*');

        $type = null;
        $topic_ids = array();
        if (isset($_POST['type'])) {
            $type = self::getEntitiesData(strip_tags($_POST['type']));
        }

        if (!empty($following_topic)) {
            foreach ($following_topic as $r) {
                $can_choose = 1;
                if ($type && $r["enable_{$type}"] != 1) {
                    $can_choose = 0;
                }

                $topic_ids[] = array(
                    'topic_id' => $r['topic_id'],
                    'topic_name' => $r['topic_name'],
                    'as_recommend' => $r['as_recommend'],
                    'can_choose' => $can_choose,
                );
            }
        }

        $this->data['data'] = $topic_ids;
        $this->display($this->data);
    }

    /**
     * 获取推荐话题
     */
    function getRecommendTopics()
    {
        $TOPIC = new TopicModule();
        $recommend_topic = $TOPIC->getRecommendTopic();

        $type = null;
        $result = array();
        if (isset($_POST['type'])) {
            $type = self::getEntitiesData(strip_tags($_POST['type']));
        }

        foreach ($recommend_topic as $topic) {
            $can_choose = 1;
            if ($type && $topic["enable_{$type}"] != 1) {
                $can_choose = 0;
            }

            $result[] = array(
                'topic_id' => $topic['topic_id'],
                'topic_name' => $topic['topic_name'],
                'topic_url' => $topic['topic_url'],
                'can_choose' => $can_choose,
            );
        }

        $this->data['data'] = $result;
        $this->display($this->data);
    }

    /**
     * 验证账号是否已经存在
     */
    function checkAccount()
    {
        $result = array();
        if (!isset($_REQUEST['account'])) {
            die(json_encode(array('error' => '账号不能为空')));
        }

        $account = $_REQUEST['account'];
        $AM = new AccountModule();
        $ret = $AM->checkAccount($account);

        if ($ret['status'] == 1) {
            $result['ok'] = '';
        } else {
            $result['error'] = $this->getStatusMessage($ret['status']);
        }

        echo json_encode($result);
    }

    /**
     * 验证帐号是否存在
     */
    function validateAccount()
    {
        $result = array();
        if (!isset($_REQUEST['account'])) {
            die(json_encode(array('error' => '账号不能为空')));
        }

        $account = htmlentities(strip_tags(trim($_REQUEST['account'])));
        $AM = new AccountModule();
        $ret = $AM->getAccountInfo($account);

        if ($ret) {
            $result['ok'] = '';
        } else {
            $result['error'] = $this->getStatusMessage(200401);
        }

        echo json_encode($result);
    }

    /**
     * 验证邀请码
     */
    function checkInviteCode()
    {
        $result = array();
        if (!isset($_REQUEST['invite_code'])) {
            die(json_encode(array('error' => '邀请码不能为空')));
        }

        $inviteCode = $_REQUEST['invite_code'];
        $IM = new InviteModule();
        $ret = $IM->checkInviteCode($inviteCode);

        if ($ret['status'] == 1) {
            $result['ok'] = '';
        } else {
            $result['error'] = $this->getStatusMessage($ret['status']);
        }

        echo json_encode($result);

    }

    /**
     * 重置邀请码
     */
    function resetInviteCode()
    {
        $this->checkLoginStatus($uid, true);
        $IM = new InviteModule();
        $ret = $IM->resetUserInviteCode($uid);
        if ($ret === false) {
            $this->data['status'] = 200424;
        } else {
            $this->data['status'] = 1;
            $this->data['message'] = '重置成功';
        }

        $this->display($this->data);
    }

    /**
     * 检查昵称
     */
    function checkNickname()
    {
        $result = array();
        if (!isset($_REQUEST['nickname'])) {
            die(json_encode(array('error' => '请输入您的昵称')));
        }

        $nickname = htmlentities(strip_tags(trim($_REQUEST['nickname'])));
        $AM = new AccountModule();
        $ret = $AM->checkNickname($nickname);

        if ($ret['status'] == 1) {
            $result['ok'] = '';
        } else {
            $result['error'] = $this->getStatusMessage($ret['status']);
        }

        echo json_encode($result);
    }

    /**
     * 检查昵称是否已经被占用(排除登录用户)
     */
    function checkNicknameExcludeLoginUser()
    {
        $result = array();
        if (!isset($_REQUEST['nickname'])) {
            die(json_encode(array('error' => '请输入您的昵称')));
        }

        $nickname = htmlentities(strip_tags(trim($_REQUEST['nickname'])));
        $userInfo = $this->getAuth('u', true);
        if (empty($userInfo['uid'])) {
            $this->display($this->result(200010));
        }

        if ($userInfo['nickname'] == $nickname) {
            $result['ok'] = '';
        } else {
            $AM = new AccountModule();
            $ret = $AM->checkNickname($nickname);

            if ($ret['status'] == 1) {
                $result['ok'] = '';
            } else {
                $result['error'] = $this->getStatusMessage($ret['status']);
            }
        }

        echo json_encode($result);
    }

    /**
     * 图片上传接口
     */
    function uploadImage()
    {
        $this->checkLoginStatus($uid, true);
        if (empty($_FILES['wangEditorH5File'])) {
            $this->display($this->result(200050));
        }

        $fileLocation = 'upload/content/' . date('Y/m/d/h/');
        $filePath = $this->getFilePath("static::{$fileLocation}");
        Helper::createFolders($filePath);

        $imgName = md5(implode(',', $_FILES['wangEditorH5File']));
        $IM = new UploadImages('wangEditorH5File', $imgName);
        $IM->setSavePath($filePath);
        $upload_info = $IM->save();
        if ($upload_info['status'] != 'ok') {
            $this->data['status'] = 200060;
            $this->data['data'] = $upload_info['message'];
        } else {
            $baseStorage = $this->config->get('url', 'request') . '/static/';
            $this->data['status'] = 1;
            $this->data['data'] = array(
                'local' => 1,
                'storage' => $baseStorage,
                'origin' => $fileLocation . $upload_info['message']['url'],
            );
        }

        $this->display($this->data);
    }

    /**
     * 图片上传接口
     *
     * @cp_params title_id
     */
    function uploadContentImage()
    {
        $this->checkLoginStatus($uid, true);
        if (empty($_FILES['wangEditorH5File'])) {
            $this->display($this->result(200050));
        }

        if (empty($this->params['title_id'])) {
            $this->display($this->result(200051));
        }

        $title_id = (int)$this->params['title_id'];
        $fileLocation = 'upload/' . $title_id . '/' . date('Y/m/d/h/');
        $filePath = $this->getFilePath("static::{$fileLocation}");
        Helper::createFolders($filePath);

        $imgName = md5(implode(',', $_FILES['wangEditorH5File']));
        $IM = new UploadImages('wangEditorH5File', $imgName);
        $IM->setSavePath($filePath);
        $upload_info = $IM->save();
        if ($upload_info['status'] != 'ok') {
            $this->data['status'] = 200060;
            $this->data['data'] = $upload_info['message'];
        } else {
            $baseStorage = $this->config->get('url', 'request') . '/static/';
            $this->data['status'] = 1;
            $this->data['data'] = array(
                'local' => 1,
                'storage' => $baseStorage,
                'origin' => $fileLocation . $upload_info['message']['url'],
            );
        }

        $this->display($this->data);
    }

    /**
     * @cp_params uid, full=0
     */
    function reward()
    {
        $uid = $this->params['uid'];
        $full = $this->params['full'];

        $AC = new AccountModule();
        $FV = new ForumView();
        $info = $AC->getAccountInfoByUid($uid, 'qr');

        if ($full) {
            $size = 'width:100%';
            $layer_size = array('80%', '');
        } else {
            $size = 'width:500px;';
            $layer_size = array('500px', '500px');
        }

        if ($info['qr']) {
            $this->data['status'] = 1;
            $this->data['data'] = array(
                'img' => sprintf('<img src="%s" style="%s">', $FV->res($info['qr']), $size),
                'layer_size' => $layer_size,
            );
        } else {
            $this->data['status'] = 200025;
        }

        $this->display($this->data);
    }

    /**
     * 回答投票
     */
    function vote()
    {
        $userInfo = $this->getAuth('u', true);
        if (empty($userInfo['uid'])) {
            $this->display($this->result(200010));
        }

        if (!$this->is_post()) {
            $this->display($this->result(200020));
        }

        if (empty($_REQUEST['act']) || empty($_REQUEST['question_id']) || empty($_REQUEST['answer_id'])) {
            $this->display($this->result(200030));
        }

        $allow_act = array('up' => true, 'down' => true);
        $answer_id = (int)$_REQUEST['answer_id'];
        $question_id = (int)$_REQUEST['question_id'];
        $act = $_REQUEST['act'];
        if (!isset($allow_act[$act])) {
            $this->display($this->result(200040));
        }

        $QM = new QuestionModule();
        $ret = $QM->updateAnswerVote($userInfo['uid'], $question_id, $answer_id, $act);
        if ($ret['status'] == 1) {
            $this->data['status'] = 1;
            $this->data['message'] = '操作成功';
            $this->data['data'] = $ret['message'];
        } else {
            $this->data = $ret;
        }

        $this->display($this->data);
    }

    /**
     * 加载被屏蔽的问题答案
     */
    function loadBlockAnswer()
    {
        $this->checkLoginStatus($uid);
        $question_id = &$_POST['question_id'];
        if ($question_id) {
            $page = array();
            $QUESTION = new QuestionModule();
            $answer_list = $QUESTION->findQuestionAnswer($uid, $question_id, 2, $page, 'a.status <> 1', false);
            if ($answer_list) {
                $content = '';
                foreach ($answer_list as $answer) {
                    $content .= $this->view->obRenderTpl('content/segment/answer', $answer);
                }

                echo $content;
            }
        }
    }

    /**
     * 加载被屏蔽的帖子回复
     */
    function loadBlockReply()
    {
        $this->checkLoginStatus($uid);
        $posts_id = &$_POST['posts_id'];
        if ($posts_id) {
            $page = array();
            $PM = new PostsModule();
            $reply_list = $PM->findReply($uid, $posts_id, 2, $page, array('<>', 1), false);
            if ($reply_list) {
                $content = '';
                foreach ($reply_list as $reply) {
                    $content .= $this->view->obRenderTpl('content/segment/reply', $reply);
                }

                echo $content;
            }
        }
    }

    /**
     * 加载被屏蔽的文章评论
     */
    function loadBlockComment()
    {
        $article_id = &$_POST['article_id'];
        if ($article_id) {
            $page = array();
            $AM = new ArticleModule();
            $comment_list = $AM->findComment($article_id, $page, array('<>', 1), false);
            if ($comment_list) {
                $content = '';
                foreach ($comment_list as $comment) {
                    $content .= $this->view->obRenderTpl('content/segment/comment', $comment);
                }

                echo $content;
            }
        }
    }

    /**
     * 文章投票接口
     */
    function articleVote()
    {
        $this->checkLoginStatus($uid, true);
        $article_id = (int)$_POST['article_id'];

        $ART = new ArticleModule();
        $artInfo = $ART->getArticleBaseInfo($article_id);

        if (empty($artInfo)) {
            $this->display($this->result(200230));
        }

        $ret = $ART->articleVote($uid, $artInfo['title_id'], $article_id);
        $this->data['status'] = $ret['status'];
        $this->data['data'] = $ret['message'];

        $this->display($this->data);
    }

    /**
     * 返回用户协议
     */
    function getAgreement()
    {
        $agreement = $this->parseGetFile('config::agreement.config.php', true);
        echo nl2br($agreement);
    }

    /**
     * 邀请回答
     */
    function invite()
    {
        $uid = (int)$_POST['uid'];
        $title_id = (int)$_POST['title_id'];

        $QM = new QuestionModule();
        $ret = $QM->inviteUser($uid, $title_id);
        $this->data['status'] = $ret['status'];

        $this->display($this->data);
    }

    /**
     * 忽略邀请
     */
    function ignoreInvite()
    {
        $this->checkLoginStatus($uid, true);
        if (!empty($_POST['invite_id'])) {
            $QM = new QuestionModule();
            $ret = $QM->ignoreInvite((int)$_POST['invite_id']);
            if ($ret) {
                $this->data['status'] = 1;
            } else {
                $this->data['status'] = 200810;
            }
        } else {
            $this->data['status'] = 200810;
        }
        $this->display($this->data);
    }

    /**
     * 举报
     */
    function report()
    {
        $id = &$_POST['id'];
        $type = &$_POST['type'];
        $this->checkLoginStatus($uid);
        if ($id && $type) {
            $RM = new ReportModule();
            $RM->add($type, $id, $uid);
        }

        $this->display($this->data);
    }

    /**
     * 回复相关操作
     */
    function replyUp()
    {
        $this->checkLoginStatus($uid, true);
        if (empty($_POST['reply_id'])) {
            $this->display($this->result(200700));
        }

        $reply_id = (int)$_POST['reply_id'];
        $POST = new PostsModule();
        $ret = $POST->replyUp($uid, $reply_id);

        if ($ret['status'] == 1) {
            $this->data['status'] = 1;
            $this->data['data'] = $ret['message'];
        } else {
            $this->data['status'] = $ret['status'];
            $this->data['message'] = $ret['message'];
        }

        $this->display($this->data);
    }

    /**
     * 推荐用户列表
     */
    function recommendUser()
    {
        $recommend_uid = array();
        $this->checkLoginStatus($loginUid);
        $RECOMMEND = new RecommendModule();
        $recommend_list = $RECOMMEND->recommendUser($loginUid, $recommend_uid);

        $data['recommend_uid'] = $recommend_uid;
        $data['recommend_list'] = $recommend_list;

        $this->data['data'] = $data;
        $this->display($this->data);
    }

    /**
     * 根据话题获取推荐用户
     */
    function followTopicUsers()
    {
        $topic_ids = &$_POST['topic_ids'];
        if (empty($topic_ids)) {
            $RECOMMEND = new RecommendModule();
            $user_list = $RECOMMEND->getSiteRecommendUser(4);
            $type = 'recommend';
        } else {
            $FOLLOWING = new FollowingModule();
            $user_list = $FOLLOWING->getTopicAnswerUser($topic_ids, 4);
            $type = 'answer';
        }

        $FV = new ForumView();
        if (!empty($user_list)) {
            foreach ($user_list as &$u) {
                $u['avatar'] = $FV->resAbsoluteUrl($u['avatar']);
                $u['homepage'] = $FV->url('user:detail', array('account' => $u['account']));
            }
        }

        $this->data['status'] = 1;
        $this->data['data']['user_list'] = $user_list;
        $this->data['data']['t'] = $type;
        $this->display($this->data);
    }

    /**
     * 按昵称搜索用户
     */
    function searchUsers()
    {
        if (empty($_POST['username'])) {
            $this->display($this->result(200800));
        }

        $username = self::getEntitiesData(strip_tags($_POST['username']));
        $ACCOUNT = new AccountModule();
        $user_list = $ACCOUNT->findAccountByNickname($username);

        $FV = new ForumView();
        if (!empty($user_list)) {
            foreach ($user_list as &$u) {
                $u['avatar'] = $FV->resAbsoluteUrl($u['avatar']);
                $u['homepage'] = $FV->url('user:detail', array('account' => $u['account']));
            }
        }

        $this->data['status'] = 1;
        $this->data['data']['user_list'] = $user_list;
        $this->display($this->data);
    }

    /**
     * 用户文章分类
     */
    function userArticleCategory()
    {
        $this->checkLoginStatus($uid, true);
        $ARTICLE = new ArticleModule();

        $category = $ARTICLE->getUserCategory($uid);
        $this->data['status'] = 1;
        $this->data['data'] = $category;

        $this->display($this->data);
    }

    /**
     * 收藏
     */
    function collection()
    {
        $title_id = (int)$_POST['title_id'];
        $this->checkLoginStatus($loginUid, true);

        $COLLECTION = new CollectionModule();
        $ret = $COLLECTION->save($loginUid, $title_id);

        $this->data['status'] = $ret['status'];
        $this->data['data'] = $ret['message'];

        $this->display($this->data);
    }

    /**
     * 关注
     *
     * @cp_params type
     */
    function following()
    {
        $type = $this->params['type'];
        $this->checkLoginStatus($loginUid, true);

        $allow_following_type = array('user' => 1, 'content' => 1, 'topic' => 1);
        if (!isset($allow_following_type[$type])) {
            $this->data['status'] = 200500;
            $this->display($this->data);
        }

        $FOLLOWING = new FollowingModule();
        switch ($type) {
            case 'user':
                if (empty($_POST['uid'])) {
                    $ret = $this->result(200502);
                } else {
                    $uid = (int)$_POST['uid'];
                    $get_statistics_info = false;
                    if (isset($_POST['get_statistics_info'])) {
                        $get_statistics_info = (bool)$_POST['get_statistics_info'];
                    }
                    $ret = $FOLLOWING->user($loginUid, $uid, $get_statistics_info);
                }
                break;

            case 'topic':
                if (empty($_POST['topic_id'])) {
                    $ret = $this->result(200502);
                } else {
                    $topic_id = (int)$_POST['topic_id'];
                    $ret = $FOLLOWING->topic($loginUid, $topic_id);
                }
                break;

            case 'content':
                if (empty($_POST['title_id']) || empty($_POST['content_type'])) {
                    $ret = $this->result(200502);
                } else {
                    $title_id = (int)$_POST['title_id'];
                    $content_type = (int)$_POST['content_type'];
                    $ret = $FOLLOWING->content($loginUid, $title_id, $content_type);
                }
                break;

            default:
                $ret = $this->result(1, 'ok');
                break;
        }

        $this->data['status'] = $ret['status'];
        $this->data['data'] = $ret['message'];

        $this->display($this->data);
    }

    /**
     * 取消关注
     *
     * @cp_params type
     */
    function unFollowing()
    {
        $type = $this->params['type'];
        $this->checkLoginStatus($loginUid, true);

        $allow_following_type = array('user' => 1, 'content' => 1);
        if (!isset($allow_following_type[$type])) {
            $this->data['status'] = 200500;
            $this->display($this->data);
        }

        $uid = (int)$_POST['uid'];
        $FOLLOWING = new FollowingModule();
        switch ($type) {
            case 'user':
                $ret = $FOLLOWING->unFollowingUser($loginUid, $uid);
                break;

            default:
                $ret = $this->result(1, 'ok');
                break;
        }

        $this->data['status'] = $ret['status'];
        $this->data['data'] = $ret['message'];
        $this->display($ret);
    }

    /**
     * 新消息数量
     */
    function getNewMessageCount()
    {
        $this->checkLoginStatus($loginUid, true);
        $MG = new MessageModule();
        $data = $MG->getNewMessageCount($loginUid);

        $this->data['status'] = 1;
        $this->data['data'] = $data;
        $this->display($this->data);
    }

    /**
     * 删除消息
     */
    function delMessage()
    {
        $this->checkLoginStatus($loginUid, true);
        if (empty($_POST['message_id'])) {
            $this->display($this->result(200700));
        }

        $message_id = (int)$_POST['message_id'];
        $MG = new MessageModule();
        $ret = $MG->deleteMessage($loginUid, $message_id);
        $this->display($ret);
    }

    /**
     * 删除会话
     */
    function delMessageDialog()
    {
        $this->checkLoginStatus($loginUid, true);
        if (empty($_POST['dialog_id'])) {
            $this->display($this->result(200700));
        }

        $dialog_id = (int)$_POST['dialog_id'];
        $MG = new MessageModule();
        $ret = $MG->deleteMessageDialog($loginUid, $dialog_id);
        $this->display($ret);
    }

    /**
     * API数据格式统一
     *
     * @param null $data
     * @param string $method
     * @param int $http_response_status
     */
    function display($data = null, $method = null, $http_response_status = 200)
    {
        if ($data['status'] != 1 && $data['message'] == 'ok') {
            $data['message'] = $this->getStatusMessage($data['status']);
        }

        $apiData['status'] = $data['status'];
        $apiData['message'] = $data['message'];
        if (!isset($data['data'])) {
            $apiData['data'] = array();
        } else {
            $apiData['data'] = $data['data'];
        }

        $this->response->setContentType('json')->display(json_encode($apiData));
        exit(0);
    }

    /**
     * @param int $status
     * @param string $message
     * @param bool $json_encode
     * @return array|string
     * @throws \Cross\Exception\CoreException
     */
    function result($status = 1, $message = 'ok', $json_encode = false)
    {
        if ($message == 'ok') {
            $message = $this->getStatusMessage($status);
        }

        return parent::result($status, $message, $json_encode);
    }

    /**
     * 获取登陆用户uid
     *
     * @param $uid
     * @param bool $response
     * @return int
     */
    private function checkLoginStatus(&$uid, $response = false)
    {
        $userInfo = $this->getAuth('u', true);
        if ($response) {
            if (empty($userInfo['uid'])) {
                $uid = 0;
                $this->display($this->result(200010));
            } else {
                $uid = $userInfo['uid'];
            }

            return true;
        } else {
            if (empty($userInfo['uid'])) {
                $uid = 0;
                return false;
            }

            $uid = $userInfo['uid'];
            return true;
        }
    }

    /**
     * 获取消息状态内容
     *
     * @param int $status
     * @return string
     */
    private function getStatusMessage($status)
    {
        static $notice = null;
        if ($notice === null) {
            $notice = $this->parseGetFile('config::notice.config.php');
        }

        if (isset($notice[$status])) {
            $message = $notice[$status];
        } else {
            $message = 'ok';
        }

        return $message;
    }

    /**
     * @return mixed
     */
    function index()
    {

    }
}
