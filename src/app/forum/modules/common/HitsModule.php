<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * HitsModule.php
 */

namespace app\forum\modules\common;

use Cross\Core\Helper;

/**
 * 点击统计
 *
 * @Auth: cmz <393418737@qq.com>
 * Class HitsModule
 * @package modules\common
 */
class HitsModule extends BaseModule
{
    /**
     * 更新点击数量的时间间隔
     *
     * @var int
     */
    protected $schedule = 3600;

    /**
     * 保存点击计数
     *
     * @param int $type
     * @param array $addition_data 既是数据又是更新条件
     * <pre>
     * posts array('posts_id' => posts_id)
     * article array('article_id' => article_id)
     * question array('question_id' => question_id)
     * </pre>
     * @param int $hits_update_time
     * @return bool
     * @throws \Cross\Exception\CoreException
     */
    function add($type, array $addition_data, $hits_update_time = 0)
    {
        $hitsTypeDBMap = array(
            BaseModule::TYPE_POSTS => $this->hits_posts,
            BaseModule::TYPE_ARTICLE => $this->hits_articles,
            BaseModule::TYPE_QUESTION => $this->hits_questions
        );

        if (!isset($hitsTypeDBMap[$type])) {
            return false;
        }

        $table = $hitsTypeDBMap[$type];

        //保存点击数据
        $ip = Helper::getLongIp();
        $save_data = $addition_data;
        $save_data['ip'] = $ip;
        $save_data['hits'] = 1;
        $this->link->insert($table, $save_data)
            ->on('DUPLICATE KEY UPDATE hits=hits+1')->stmtExecute();

        //更新点击数(1个IP只算1次)
        if ($hits_update_time == 0 || TIME - $hits_update_time >= $this->schedule) {
            $hits = $this->link->get($table, 'count(1) total', $addition_data);
            $this->updateContentHits($type, $addition_data, $hits['total']);
        }

        return true;
    }

    /**
     * 更新内容表中的点击次数
     *
     * @param int $type
     * @param array $condition
     * @param int $hits
     * @return bool
     */
    private function updateContentHits($type, array $condition, $hits)
    {
        $type2ContentTable = array(
            BaseModule::TYPE_POSTS => $this->posts,
            BaseModule::TYPE_ARTICLE => $this->articles,
            BaseModule::TYPE_QUESTION => $this->questions
        );

        if (!isset($type2ContentTable[$type])) {
            return false;
        }

        $table = $type2ContentTable[$type];
        return $this->link->update($table, array('hits' => (int)$hits, 'hits_update_time' => TIME), $condition);
    }
}
