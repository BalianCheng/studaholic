<?php
/**
 * 回复帖子
 *
 * @Auth: cmz <393418737@qq.com>
 * posts_reply.php
 */
$relation_data = $data['relation_data'];
$act_user_home_link = $this->url('user:detail', array('account' => $data['account']));
$reply_link = $this->url('content:posts', array('posts_id' => $relation_data['posts_id']));
?>
<div class="row">
    <div class="col-md-12">
        <div class="media">
            <div class="media-body">
                <div class="user-info">
                    <?php echo $this->a($data['nickname'], $act_user_home_link, array('class' => 'ia')) ?> 回复了帖子
                </div>
                <div class="media-heading">
                    <?php echo $this->a($data['title'], $reply_link) ?>
                </div>
                <div class="content">
                    <a href="<?php echo $reply_link . "#reply_{$relation_data['reply_id']}" ?>" class="ia">
                        <?php echo $relation_data['content'] ?>
                    </a>
                </div>
            </div>
            <div class="media-right" style="padding-top:10px">
                <a href="<?php echo $act_user_home_link ?>">
                    <?php echo $this->userAvatar($data['avatar']) ?>
                </a>
            </div>
        </div>
    </div>
</div>

