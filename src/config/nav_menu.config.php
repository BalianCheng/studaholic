<?php
/**
 * @Auth wonli <wonli@live.com>
 * nav_menu.config.php
 *
 * xs=[1|0]在移动模版下是否显示
 * type=[1|2]等于1时link参数格式为内部控制器:方法, 等于2时link参数为绝对路径
 * login_display [1|0]是否登录后才显示
 * target 默认在当前页面打开, 在新窗口打开请设置为_blank
 * current 控制器名称为当前值时设置选择状态
 */
return array(
    array(
        'xs' => 1,
        'name' => '动态',
        'type' => 1,
        'link' => '',
        'login_display' => 1,
        'current' => 'main'
    ),
    array(
        'xs' => 1,
        'name' => '发现',
        'type' => 1,
        'link' => 'explore',
        'login_display' => 0,
        'current' => 'explore'
    ),
    array(
        'xs' => 1,
        'name' => '分类',
        'type' => 1,
        'link' => 'topics',
        'login_display' => 0,
        'current' => 'topics'
    )
);
