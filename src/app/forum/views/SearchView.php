<?php
/**
 * @Auth wonli <wonli@live.com>
 * SearchView.php
 */

namespace app\forum\views;

/**
 * @Auth wonli <wonli@live.com>
 * Class SearchView
 * @package app\forum\views
 */
class SearchView extends ForumView
{
    function index($data)
    {
        $this->renderTpl('search/index', $data);
    }
}
