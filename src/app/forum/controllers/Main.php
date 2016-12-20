<?php
/**
 * @Author:       cmz <393418737@qq.com>
 */
namespace app\forum\controllers;

use app\forum\modules\collection\CollectionModule;
use app\forum\modules\following\FollowingModule;
use app\forum\modules\activity\ActivityModule;
use app\forum\modules\content\QuestionModule;
use app\forum\modules\message\MessageModule;
use app\forum\modules\invite\InviteModule;
use Cross\Core\Helper;

/**
 * @Auth: cmz <393418737@qq.com>
 * Class Main
 * @package app\forum\controllers
 */
class Main extends Forum
{
    function __construct()
    {
        parent::__construct();
        if ($this->isLogin) {
            $FM = new FollowingModule();
            $tips = $FM->followContentNewTips($this->uid);
            $this->data['newTips'] = (int)$tips;

            $QM = new QuestionModule();
            $this->data['receivedInviteCount'] = $QM->getReceivedInviteCount($this->uid);
        }
    }

    /**
     * 默认控制器
     *
     * @cp_params p=1
     */
    function index()
    {
        //未登陆时跳转
        if (!$this->isLogin) {
            $this->to('explore:index');
        }

        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 50,
            'half' => 5,
            'link' => array('main:index', array())
        );

        $ACT = new ActivityModule();
        $act_list = $ACT->getActivity($this->uid, $page);

        $this->data['act_list'] = $act_list;
        $this->data['page'] = $page;

        if ($this->is_ajax_request()) {
            $this->view->showAct($act_list);
        } else {
            $this->display($this->data);
        }
    }

    /**
     * 我的关注
     *
     * @cp_params p=1
     */
    function following()
    {
        if (!$this->isLogin) {
            $this->to();
        }

        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 50,
            'half' => 5,
            'link' => array('main:following', array())
        );

        $FOLLOW = new FollowingModule();
        $follow_content = $FOLLOW->findUserFollowContent($this->uid, $page);

        $this->data['page'] = $page;
        $this->data['follow_content'] = $follow_content;
        $this->display($this->data);
    }

    /**
     * 我的收藏
     *
     * @cp_params p=1
     */
    function collections()
    {
        if (!$this->isLogin) {
            $this->to();
        }

        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 50,
            'half' => 5,
            'link' => array('main:collections', array())
        );

        $COLLECTION = new CollectionModule();
        $collections_content = $COLLECTION->findUserCollectionContent($this->uid, $page);

        $this->data['page'] = $page;
        $this->data['collections_content'] = $collections_content;
        $this->display($this->data);
    }

    /**
     * 邀请我参与的主题
     *
     * @cp_params t=new, p=1
     * @throws \Cross\Exception\CoreException
     */
    function invite()
    {
        if (!$this->isLogin) {
            $this->to();
        }

        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 50,
            'half' => 5,
            'link' => array('main:collections', array())
        );

        $filter_type_config = array('new' => '待参与', 'ignore' => '已忽略', 'finish' => '已回答');
        $filter_type = &$this->params['t'];
        if (!isset($filter_type_config[$filter_type])) {
            $filter_type = 'new';
        }

        $QM = new QuestionModule();
        $status_map = array('new' => QuestionModule::INVITE_NEW,
            'ignore' => QuestionModule::INVITE_IGNORE, 'finish' => QuestionModule::INVITE_FINISH);
        $invite_content = $QM->findUserInviteContent($this->uid, $status_map[$filter_type], $page);

        $this->data['page'] = $page;
        $this->data['invite_content'] = $invite_content;
        $this->data['filter_type_config'] = $filter_type_config;
        $this->display($this->data);
    }

    /**
     * 邀请注册
     */
    function inviteRegister()
    {
        if (!$this->isLogin) {
            $this->to();
        }

        $IM = new InviteModule();
        $inviteCode = $IM->getUserInviteCode($this->uid);

        $token = array(
            'u' => $this->uid, //UID
            'c' => $inviteCode['invite_code'], //邀请码
        );

        $tokenData = json_encode($token);
        $token = Helper::encodeParams($tokenData, 'inviteRegister');
        $invitedUser = $IM->getInviteUser($this->uid);

        $this->data['token'] = $token;
        $this->data['inviteUser'] = $invitedUser;
        $this->display($this->data);
    }

    /**
     * 我的私信
     *
     * @cp_params t=dialog, p=1
     */
    function message()
    {
        if (!$this->isLogin) {
            $this->to();
        }

        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 50,
            'half' => 5,
            'link' => array('main:message', array())
        );

        $MM = new MessageModule();

        $t = $this->params['t'];
        if ($t == 'sys') {
            $messageDialog = $MM->sysMessage($this->uid, $page);
            $msg_id = array();
            foreach ($messageDialog as $m) {
                $msg_id[] = $m['id'];
            }

            //设置为已读
            $MM->updateMessageReadTime($msg_id);
        } else {
            $messageDialog = $MM->messageDialog($this->uid, $page);
        }

        $this->data['t'] = $t;
        $this->data['page'] = $page;
        $this->data['dialog_list'] = $messageDialog;
        $this->display($this->data);
    }
}
