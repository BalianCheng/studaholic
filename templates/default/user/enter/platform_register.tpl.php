<div class="form-group">
    <button type="submit" class="form-control btn btn-primary btn-current">注册</button>
</div>

<div class="form-group">
    <div>
        你也可以用以下方式绑定注册或
        <a href="<?php echo $this->url('user:login', array('back' => $data['back_url'])) ?>">
            登录
        </a>
    </div>
    <div>
        <?php foreach($data['platform'] as $k => $d) : ?>
            <a href="<?php echo $d['link'] ?>" class="platform-link">
                <i class="iconfont-platform icon-<?php echo $k ?>"></i>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * platform_login.tpl.php
 */
