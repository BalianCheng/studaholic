<li>
    <a href="<?php echo $this->url('user:detail', array('account' => $this->data['loginUser']['account'])) ?>">
        <i class="iconfont-middle user-nav-icon icon-mine"></i>
        <span>主页</span>

    </a>
</li>
<li>
    <a href="<?php echo $this->url('settings') ?>">
        <i class="iconfont-middle user-nav-icon icon-settings"></i>
        <span>设置</span>
    </a>
</li>
<li>
    <a href="<?php echo $this->url('user:logout') ?>">
        <i class="iconfont-middle user-nav-icon icon-logout"></i>
        <span>退出</span>
    </a>
</li>
<?php
/**
 * @Auth wonli <wonli@live.com>
 * nav_menu.tpl.php
 */
