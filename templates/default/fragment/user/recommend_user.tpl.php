<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * recommend_user.tpl.php
 */
$user_home_link = $this->url('user:detail', array('account' => $data['account']));
?>
<div class="media">
    <a class="media-left" href="<?php echo $user_home_link ?>">
        <?php echo $this->userAvatar($data['avatar'], '36px') ?>
    </a>
    <div class="media-body">
        <h4 class="media-heading">
            <a href="<?php echo $user_home_link ?>" class="ia">
                <?php echo $data['nickname'] ?>
            </a>
        </h4>
        <?php echo $data['introduce'] ?>
    </div>
</div>
