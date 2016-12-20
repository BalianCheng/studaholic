<?php
namespace app\forum\modules\search;

use app\forum\modules\title\TitleModule;

/**
 * @Auth wonli <wonli@live.com>
 * SearchModule.php
 */
class SearchModule extends TitleModule
{
    /**
     * @param string $q
     * @param array $page
     * @return array
     */
    function find($q, array &$page)
    {
        $q = self::getEntitiesData(strip_tags($q));
        $condition = "title LIKE '%{$q}%' AND status=1";
        $total = $this->link->get($this->title, 'count(1) total_result', $condition);
        $page['result_count'] = $total['total_result'];
        $page['limit'] = max(1, (int)$page['limit']);
        $page['total_page'] = ceil($page['result_count'] / $page['limit']);

        $list = array();
        if ($page['p'] <= $page['total_page']) {
            $page['p'] = max(1, $page['p']);
            $start = ($page['p'] - 1) * $page['limit'];

            $listSQL = $this->link->select('title_id')
                ->from($this->title)
                ->where($condition)
                ->limit($start, $page['limit'])->getSQL(true);

            $list = $this->simpleContentListDetail($listSQL);
        }

        return $list;
    }
}
