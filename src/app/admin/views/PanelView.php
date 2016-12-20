<?php
namespace app\admin\views;

class PanelView extends ForumView
{
    function index($data = array())
    {
        $this->renderTpl('panel/index', $data);
    }
}
