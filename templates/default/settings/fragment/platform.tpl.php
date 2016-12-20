<?php
/**
 * @Auth wonli <wonli@live.com>
 * account.tpl.php
 */
$platform = &$data['platform'];
$accountInfo = &$data['account_info'];
?>
<div class="col-md-12">
    <?php if (empty($platform)) : ?>
        <div>暂不支持第三方帐号平台</div>
    <?php else : ?>
        <?php foreach ($platform as $k => $d) : ?>
            <span class="platform-bind">
                <i class="iconfont-platform icon-<?php echo $k ?>"></i>
                <?php if ($d['is_bind']): ?>
                    已绑定
                    <a href="<?php echo $this->url("connect:unbind", array('t' => $k)) ?>"
                       class="platform-link">(解除绑定)</a>
                <?php else : ?>
                    <a href="<?php echo $this->url("connect:index", array('t' => $k)) ?>" class="platform-link">绑定</a>
                <?php endif ?>
            </span>
        <?php endforeach ?>
    <?php endif ?>
</div>

