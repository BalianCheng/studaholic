<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * user_list.tpl.php
 */
$homepage = $this->url('user:detail', array('account' => $data['account']));
?>
<div class="media">
    <div class="media-left">
        <a href="<?php echo $homepage ?>">
            <?php echo $this->userAvatar($data['avatar']) ?>
        </a>
    </div>
    <div class="media-body">
        <div class="row">
            <div class="col-xs-8">
                <h5>
                    <a href="<?php echo $homepage ?>" target="_blank">
                        <?php echo $data['nickname'] ?>
                    </a>
                </h5>
                <?php echo $data['introduce'] ?>
            </div>
            <div class="col-xs-4 tar" style="margin-top:15px;">
                <?php if ($data['status'] == 1) : ?>
                    <span>正常</span>
                <?php else : ?>
                    <span>封号</span>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>

