<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Panel.php
 */
namespace app\admin\controllers;

use app\forum\modules\account\AccountModule;
use app\forum\modules\common\BaseModule;
use app\forum\modules\title\TitleModule;

class Panel extends Forum
{
    /**
     * 登录成功后默认跳转到空白的面板
     */
    function index()
    {
        //用户
        $account = new AccountModule();
        $totalUser = $account->getTotalUser();

        //内容
        $title = new TitleModule();
        $content = $title->getContentCount();

        //统计
        $registerStat = $this->getUserChartData(strtotime(date('Y-m-d')), TIME);
        $contentStat = $this->getContentChartData(strtotime(date('Y-m-d')), TIME);

        $this->data['content'] = $content;
        $this->data['totalUser'] = $totalUser;
        $this->data['contentStat'] = $contentStat;
        $this->data['registerStat'] = $registerStat;

        $this->display($this->data);
    }

    /**
     * 内容统计
     */
    function contentStat()
    {
        $start = &$_POST['s'];
        $end = &$_POST['e'];

        $start_unix_time = strtotime($start);
        $end_unix_time = strtotime($end);

        if ($start == $end) {
            if (TIME - $start_unix_time < 86400) {
                $end_unix_time = TIME;
            } else {
                $end_unix_time = $start_unix_time + 86400;
            }
        } elseif (TIME - $end_unix_time < 86400) {
            $end_unix_time = TIME;
        } else {
            $end_unix_time = strtotime($end);
        }

        $this->data['data'] = $this->getContentChartData($start_unix_time, $end_unix_time);
        $this->display($this->data, 'JSON');
    }

    /**
     * 用户统计
     */
    function userStat()
    {
        $start = &$_POST['s'];
        $end = &$_POST['e'];

        $start_unix_time = strtotime($start);
        $end_unix_time = strtotime($end);

        if ($start == $end) {
            if (TIME - $start_unix_time < 86400) {
                $end_unix_time = TIME;
            } else {
                $end_unix_time = $start_unix_time + 86400;
            }
        } elseif (TIME - $end_unix_time < 86400) {
            $end_unix_time = TIME;
        } else {
            $end_unix_time = strtotime($end);
        }

        $data = $this->getUserChartData($start_unix_time, $end_unix_time);
        $this->data['data'] = $data['data'];
        $this->data['labels'] = $data['labels'];
        $this->display($this->data, 'JSON');
    }

    /**
     * 整理内容统计数据
     *
     * @param string $start_unix_time
     * @param string $end_unix_time
     * @return array
     */
    private function getContentChartData($start_unix_time, $end_unix_time)
    {
        $TM = new TitleModule();
        $result = $TM->getContentNum($start_unix_time, $end_unix_time);

        $posts = &$result[BaseModule::TYPE_POSTS];
        $question = &$result[BaseModule::TYPE_QUESTION];
        $article = &$result[BaseModule::TYPE_ARTICLE];

        return array((int)$question, (int)$posts, (int)$article);
    }

    /**
     * 整理统计所需的数据格式
     *
     * @param string $start_unix_time
     * @param string $end_unix_time
     * @return array
     */
    private function getUserChartData($start_unix_time, $end_unix_time)
    {
        $AM = new AccountModule();
        $registerInfo = $AM->getRegisterCollectInfo($start_unix_time, $end_unix_time);

        $data = $labels = array();
        if (!empty($registerInfo)) {
            foreach ($registerInfo as $register) {
                $data[] = $register['count'];
                $labels[] = $register['date'];
            }
        }

        return array('labels' => $labels, 'data' => $data);
    }
}
