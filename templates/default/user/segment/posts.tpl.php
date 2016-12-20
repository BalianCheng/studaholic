<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * posts.tpl.php
 */
$content_link = $this->url('content:posts', array('posts_id' => $data['posts_id']));
?>
<div class="row">
    <div class="col-md-12">
        <div class="content-list-title">
            <a href="<?php echo $content_link ?>">
                <?php echo $data['title'] ?>
            </a>
        </div>

        <div class="user-info">
            发布于 <?php echo $this->fTime($data['post_time']); ?>
        </div>
    </div>
</div>
