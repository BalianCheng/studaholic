<?php
/**
 * @Author:       cmz <393418737@qq.com>
 */
namespace app\forum\views;

use app\forum\modules\activity\ActivityModule;

/**
 * @Auth: cmz <393418737@qq.com>
 * Class MainView
 * @package app\web\views
 */
class MainView extends ForumView
{
    protected $main_slide_menu = array();

    /**
     * ACT类型和模版对应关系
     *
     * @var array
     */
    protected static $act_tpl_config = array(
        ActivityModule::POSTS => 'posts',
        ActivityModule::POSTS_FOLLOW => 'posts_follow',
        ActivityModule::POSTS_REPLY => 'posts_reply',

        ActivityModule::QUESTION => 'question',
        ActivityModule::QUESTION_FOLLOW => 'question_follow',
        ActivityModule::QUESTION_ANSWER => 'question_answer',
        ActivityModule::QUESTION_ANSWER_UP => 'question_answer_up',

        ActivityModule::ARTICLE => 'article',
        ActivityModule::ARTICLE_UP => 'article_up',
        ActivityModule::ARTICLE_FOLLOW => 'article_follow',
        ActivityModule::ARTICLE_COMMENT => 'article_comment',
    );

    function __construct()
    {
        parent::__construct();
        $this->main_slide_menu = $this->loadConfig('main_slide_menu.config.php')->getAll();
    }

    /**
     * 默认视图控制器
     *
     * @param array $data
     */
    function index($data = array())
    {
        $this->renderTpl('main/index', $data);
    }

    /**
     * 动态数据
     *
     * @param $data
     */
    function showAct($data)
    {
        foreach ($data as $d) {
            $tpl = self::$act_tpl_config[$d['action_type']];
            $this->renderTpl("act/{$tpl}", $d);
        }
    }

    /**
     * 我关注的主题
     *
     * @param $data
     */
    function following($data)
    {
        $this->renderTpl('main/following_content', $data);
    }

    /**
     * 我的收藏
     *
     * @param $data
     */
    function collections($data)
    {
        $this->renderTpl('main/collections', $data);
    }

    /**
     * 邀请我参与的主题
     *
     * @param $data
     */
    function invite($data)
    {
        $this->renderTpl('main/invite', $data);
    }

    /**
     * 邀请注册
     *
     * @param $data
     */
    function inviteRegister($data)
    {
        $this->renderTpl('main/invite_register', $data);
    }

    /**
     * 我的私信
     *
     * @param $data
     */
    function message($data)
    {
        $this->renderTpl('main/message', $data);
    }
}
