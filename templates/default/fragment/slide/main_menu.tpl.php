<?php
/**
 * 主页左侧导航菜单
 *
 * @Auth: cmz <393418737@qq.com>
 * main_menu.tpl.php
 */
$newTips = 0;
if (isset($this->data['newTips'])) {
    $newTips = &$this->data['newTips'];
}

$receivedInviteCount = 0;
if (isset($this->data['receivedInviteCount'])) {
    $receivedInviteCount = &$this->data['receivedInviteCount'];
}

if (!empty($data)) {
    foreach ($data as $menu_name => $menu_config) {
        if ($this->action == $menu_config['current_action']) {
            $attr = array('role' => 'presentation', 'class' => 'active');
        } else {
            $attr = array('role' => 'presentation');
        }

        $menu_icon = '';
        if (isset($menu_config['iconfont'])) {
            $menu_icon = $this->htmlTag('i', array('class' => "{$menu_config['iconfont']} main-side-icon ia"));
        }

        $menu_name = $menu_icon  . $menu_name;
        if (($menu_config['current_action'] == 'invite' && $receivedInviteCount) ||
            ($menu_config['current_action'] == 'following' && $newTips)
        ) {
            $menu_name .= $this->img($this->res('images/dot.png'), array('style' => 'margin:0 0 2px 10px'));
        }

        echo $this->wrap('li', $attr)->a($menu_name, $this->url($menu_config['url']));
    }
}
