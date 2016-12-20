<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * ReportModule.php
 */

namespace app\forum\modules\common;

/**
 * @Auth: cmz <393418737@qq.com>
 * Class ReportModule
 * @package app\forum\modules\common
 */
class ReportModule extends BaseModule
{
    /**
     * 类型名称对应的数字ID
     *
     * @var array
     */
    static $reportNameMap = array(
        'answer' => 1, //问题答案
        'reply' => 2, //帖子回复
        'comment' => 3, //文章评论
    );

    /**
     * 增加举报记录
     *
     * @param string $type_name
     * @param int $id
     * @param int $uid
     * @return bool
     */
    function add($type_name, $id, $uid = 0)
    {
        if (isset(self::$reportNameMap[$type_name])) {
            $this->link->insert($this->report, array(
                'type' => self::$reportNameMap[$type_name],
                'report_id' => (int)$id,
                'report_uid' => (int)$uid,
                'rt' => TIME
            ))->on('DUPLICATE KEY UPDATE rt=' . TIME)->stmt()->execute();
        }

        return true;
    }

    /**
     * 获取举报记录
     *
     * @param int $id
     * @param string $fields
     * @return mixed
     */
    function get($id, $fields = '*')
    {
        return $this->link->get($this->report, $fields, array(
            'id' => (int)$id
        ));
    }

    /**
     * 删除举报记录
     *
     * @param int $id
     * @return bool
     * @throws \Cross\Exception\CoreException
     */
    function del($id)
    {
        return $this->link->del($this->report, array('id' => (int)$id));
    }

    /**
     * 举报内容列表
     *
     * @param array $page
     * @return mixed
     */
    function findReport(&$page)
    {
        return $this->link->find("{$this->report} r
            LEFT JOIN {$this->answers} a ON r.type=1 AND r.report_id=a.answer_id
            LEFT JOIN {$this->reply} fr ON r.type=2 AND r.report_id=fr.reply_id
            LEFT JOIN {$this->articles_comment} ac ON r.type=3 AND r.report_id=ac.comment_id
            LEFT JOIN {$this->user} u ON r.report_uid=u.uid",
            'r.*, u.nickname report_nickname, fr.reply_content, ac.comment_content, a.answer_content', array(), '1 DESC', $page
        );
    }
}
