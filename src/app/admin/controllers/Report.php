<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Report.php
 */

namespace app\admin\controllers;


use app\forum\modules\common\ReportModule;
use app\forum\modules\content\ArticleModule;
use app\forum\modules\content\PostsModule;
use app\forum\modules\content\QuestionModule;

/**
 * 反馈
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Report
 * @package app\admin\controllers
 */
class Report extends Forum
{
    /**
     * 违规内容
     *
     * @cp_params p=1
     */
    function violation()
    {
        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 30
        );

        $RM = new ReportModule();
        $reportList = $RM->findReport($page);
        $this->data['page'] = $page;
        $this->data['report_list'] = $reportList;

        $this->display($this->data);
    }

    /**
     * 举报操作
     *
     * @cp_params type, id
     */
    function action()
    {
        $type = $this->params['type'];
        $actionTypeToStatus = array('hide' => -1, 'block' => -2);
        if(!isset($actionTypeToStatus[$type])) {
            $this->to('report:violation');
        }

        $RM = new ReportModule();
        $id = (int)$this->params['id'];
        $report_info = $RM->get($id);
        if(empty($report_info)) {
            $this->to('report:violation');
        }

        $report_id = $report_info['report_id'];
        $interact_type = $report_info['type'];
        $status = $actionTypeToStatus[$type];
        switch ($interact_type) {
            case 1:
                $QM = new QuestionModule();
                $QM->updateAnswerStatus($report_id, $status);
                break;

            case 2:
                $PM = new PostsModule();
                $PM->updateReplyStatus($report_id, $status);
                break;

            case 3:
                $AM = new ArticleModule();
                $AM->updateCommentStatus($report_id, $status);
                break;
        }

        $RM->del($id);
        $this->return_referer();
    }

    /**
     * 忽略举报信息
     *
     * @cp_params id
     */
    function ignore()
    {
        $id = (int) $this->params['id'];
        $RM = new ReportModule();
        $RM->del($id);

        $this->return_referer();
    }
}
