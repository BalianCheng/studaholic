<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * ExploreView.php
 */

namespace app\forum\views;

/**
 * 发现视图控制器
 *
 * @Auth: cmz <393418737@qq.com>
 * Class ExploreView
 * @package app\forum\views
 */
class ExploreView extends ForumView
{
    /**
     * 发现首页
     *
     * @param array $data
     */
    function index($data = array())
    {
        if ($data['mode'] == 0) {
            $this->renderTpl('explore/index', $data);
        } else {
            $this->renderTpl('explore/single', $data);
        }
    }
}
