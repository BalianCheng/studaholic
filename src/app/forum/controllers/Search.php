<?php
/**
 * @Auth wonli <wonli@live.com>
 * Search.php
 */

namespace app\forum\controllers;

use app\forum\modules\search\SearchModule;

/**
 * 搜索
 *
 * @Auth wonli <wonli@live.com>
 * Class Search
 * @package app\forum\controllers
 */
class Search extends Forum
{
    /**
     * 简单搜索
     *
     * @cp_params q, p=1
     * @return mixed
     */
    function index()
    {
        $q = $this->params['q'];
        $page = array(
            'p' => (int)$this->params['p'],
            'limit' => 50,
            'half' => 5,
            'link' => array('search:index', array('q' => $q))
        );

        $SM = new SearchModule();
        $result = $SM->find($q, $page);

        if(!empty($result)) {
            $this->data['seo_title'] = "关键词 {$q} 搜索结果";
        } else {
            $this->data['seo_title'] = "暂无相关内容";
        }

        $this->data['page'] = $page;
        $this->data['result'] = $result;

        $this->display($this->data);
    }
}
