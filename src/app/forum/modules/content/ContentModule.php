<?php
namespace app\forum\modules\content;

use app\forum\modules\common\BaseModule;
use app\forum\modules\topic\TopicModule;

/**
 * @Auth: cmz <393418737@qq.com>
 * ContentModule.php
 */
class ContentModule extends BaseModule
{
    const INVITE_NEW = 0; //未处理
    const INVITE_IGNORE = 1; //忽略
    const INVITE_FINISH = 2; //已完成

    /**
     * 相关内容
     *
     * @param string $topic_ids
     * @param int $type
     * @param int $limit
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    function getCorrelationContent($topic_ids, $type, $limit = 10)
    {
        $t = $this->link->select('title, type, title_id')->from($this->title)
            ->where("topic_ids = '{$topic_ids}' AND type={$type} AND status=1")
            ->orderBy('interact_count DESC')->limit($limit)
            ->getSQL(true);

        $data = $this->link->select('t.*, q.question_id, p.posts_id, a.article_id')
            ->from("({$t}) t LEFT JOIN {$this->questions} q ON t.type=1 AND t.title_id=q.title_id
                LEFT JOIN {$this->posts} p ON t.type=2 AND t.title_id=p.title_id
                LEFT JOIN {$this->articles} a ON t.type = 3 AND t.title_id = a.title_id"
            )->stmt()->fetchAll(\PDO::FETCH_ASSOC);

        return $data;
    }

    /**
     * 基础内容的附加信息
     *
     * @param string $base_sql
     * @param $uid
     * @return mixed
     * @throws \Cross\Exception\CoreException
     */
    protected function getContentBaseExtendInfo($base_sql, $uid)
    {
        $question_base_build = $this->link->select('q.*, u.nickname, u.account, u.avatar, c.id collection_id, fc.id follow_id, count(ffc.id) total_follow')
            ->from("({$base_sql}) q LEFT JOIN {$this->user} u ON q.uid=u.uid
                LEFT JOIN {$this->collections} c ON (c.uid={$uid} AND q.title_id=c.title_id)
                LEFT JOIN {$this->following_content} fc ON (fc.uid={$uid}) and q.title_id=fc.title_id
                LEFT JOIN {$this->following_content} ffc ON q.title_id=ffc.title_id");

        $content_base_info = $question_base_build->stmt()->fetch(\PDO::FETCH_ASSOC);
        if (empty($content_base_info['title_id'])) {
            return array();
        }

        //所属话题
        $topics_name_string = '';
        $topics_ids = $topics_info = $topics_name = array();
        if (!empty($content_base_info['topic_ids'])) {
            $TOPIC = new TopicModule();
            $topics_info = $TOPIC->getTopicInfo4Strings($content_base_info['topic_ids'], 'topic_id, topic_name, topic_url');
            array_walk($topics_info, function (&$topic) use (&$topics_name, &$topics_ids) {
                $topics_ids[] = $topic['topic_id'];
                $topics_name[] = $topic['topic_name'];
                $topic['can_choose'] = 1;
            });
            $topics_name_string = implode(',', $topics_name);
        }

        //是否允许编辑和追加内容
        $content_base_info['can_append'] = 0;
        $editors = $this->getTopicEditor($topics_ids);
        if ($uid > 0 && $uid == $content_base_info['uid']) {
            $content_base_info['can_edit'] = 1;
            $content_base_info['can_append'] = 1;
        } elseif ($uid > 0 && isset($editors[$uid])) {
            $content_base_info['can_edit'] = 1;
        } else {
            $content_base_info['can_edit'] = 0;
        }

        $content_base_info['topics'] = $topics_info;
        $content_base_info['topics_names'] = $topics_name_string;
        $content_base_info['content_type'] = self::$typeMap[$content_base_info['type']];
        return $content_base_info;
    }

    /**
     * 更新用户邀请状态
     *
     * @param $invite_id
     * @param $status
     * @return bool
     */
    function updateInviteStatus($invite_id, $status)
    {
        return $this->link->update($this->invite, array('status' => (int)$status), array('id' => (int)$invite_id));
    }

    /**
     * 有权限编辑话题的人
     * <pre>
     * 当内容没有设置话题的时候, 所有编辑都有权限编辑话题
     * 当指定话题时, 只有该话题下的编辑有权限编辑内容
     * </pre>
     *
     * @param array $topic_ids
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    private function getTopicEditor(array $topic_ids = array())
    {
        $condition = array();
        if (!empty($topic_ids)) {
            //topics_id = 0 所有话题管理权限
            $topic_ids[] = 0;
            $condition = array('topic_id' => array('IN', $topic_ids));
        }

        $topic_editors = array();
        $this->link->select('editor_uid')
            ->from($this->topic_editor)->where($condition)
            ->stmt()->fetchAll(\PDO::FETCH_FUNC, function ($editor_uid) use (&$topic_editors) {
                $editor = explode(',', $editor_uid);
                $topic_editors = array_merge($topic_editors, $editor);
            });

        $result = array();
        if (!empty($topic_editors)) {
            foreach ($topic_editors as $editor) {
                $editor = trim($editor);
                $result[$editor] = $editor;
            }
        }

        return $result;
    }
}
