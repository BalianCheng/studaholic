<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * main_slide_menu.config.php
 */
return array(
    '最新动态' => array(
        'current_action' => 'index',
        'iconfont' => 'iconfont-middle icon-act',
        'url' => '',
    ),
    '我关注的主题' => array(
        'current_action' => 'following',
        'iconfont' => 'iconfont-middle icon-follow',
        'url' => 'main:following'
    ),
    '我的收藏' => array(
        'current_action' => 'collections',
        'iconfont' => 'iconfont-middle icon-collect',
        'url' => 'main:collections'
    ),
    '邀请我参与的主题' => array(
        'current_action' => 'invite',
        'iconfont' => 'iconfont-middle icon-invite',
        'url' => 'main:invite'
    ),
    '邀请好友' => array(
        'current_action' => 'inviteRegister',
        'iconfont' => 'iconfont-middle icon-invite-register',
        'url' => 'main:inviteRegister'
    ),
    '我的私信' => array(
        'current_action' => 'message',
        'iconfont' => 'iconfont-middle icon-message',
        'url' => 'main:message'
    )
);
