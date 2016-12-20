<?php
/**
 * @Auth wonli <wonli@live.com>
 * SettingView.php
 */

namespace app\forum\views;

/**
 * 个人设置
 *
 * @Auth wonli <wonli@live.com>
 * Class SettingsView
 * @package app\forum\views
 */
class SettingsView extends ForumView
{
    function index($data)
    {
        $this->renderTpl('settings/index', $data);
    }
}
