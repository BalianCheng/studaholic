<div class="form-group">
    <button type="submit" class="form-control btn btn-primary btn-current">登录</button>
</div>

<div class="form-group">
    <div>
        你也可以用以下方式登录或
        <a href="<?php echo $this->url('user:register', array('back' => $data['back_url'])) ?>">
            注册
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
