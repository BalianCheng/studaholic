<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * posts.tpl.php
 */
$user_home = $this->url('user:detail', array('account' => $data['account']));
?>
<div class="row">
    <div class="col-md-12">
        <a href="<?php echo $this->url('content:posts', array('posts_id' => $data['posts_id'])) ?>" target="_blank">
            [帖子] <?php echo $data['title'] ?>
        </a>
    </div>
</div>

