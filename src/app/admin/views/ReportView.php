<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * ReportView.php
 */

namespace app\admin\views;

/**
 * @Auth: cmz <393418737@qq.com>
 * Class ReportView
 * @package app\admin\views
 */
class ReportView extends ForumView
{
    /**
     * 违规内容
     *
     * @param array $data
     */
    function violation($data = array())
    {
        $report_list = &$data['report_list'];
        foreach ($report_list as &$report) {
            switch ($report['type']) {
                case 1:
                    $key = 'answer_content';
                    $interact_type = '问题答案';
                    unset($report['reply_content'], $report['comment_content']);
                    break;

                case 2:
                    $key = 'reply_content';
                    $interact_type = '帖子回复';
                    unset($report['comment_content'], $report['answer_content']);
                    break;

                case 3:
                    $key = 'comment_content';
                    $interact_type = '文章评论';
                    unset($report['reply_content'], $report['answer_content']);
                    break;

                default:
                    $key = null;
                    $interact_type = '';
            }

            $report['interact_type'] = $interact_type;
            $report['report_content'] = '';
            if ($key) {
                $report['report_content'] = $report[$key];
                unset($report[$key]);
            }
        }

        $this->renderTpl('report/violation', $data);
    }
}
