<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * posts.tpl.php
 */
$images = &$data['images'];
$interact_count = $this->e($data, 'interact_count');
$content_link = $this->url('content:posts', array('posts_id' => $data['posts_id']));
?>
<div class="row">
    <div class="col-md-12">
        <div class="media">
            <div class="media-left tac">
                <span class="btn btn-count">
                    <?php echo $this->fCount($interact_count) ?>
                </span>
                <br/>
                <span title="回复">回</span>
            </div>
            <div class="media-body">
                <a href="<?php echo $content_link ?>">
                    <?php echo $data['title'] ?>
                </a>
                <h5 class="user-info">
                    <a title="<?php echo $data['nickname']; ?>"
                       href="<?php echo $this->url('user:detail', array('account' => $data['account'])) ?>">
                        <?php echo $this->userAvatar($data['avatar'], '24px') ?>
                    </a>
                    <?php
                    echo $this->userNickname($data['account'], $data['nickname'], $data['introduce'], true, array('class' => 'ia')) .
                        $this->xsHideFTime($data['post_time'], ' 发布了帖子于 ');
                    ?>
                </h5>
                <div class="content">
                    <?php
                    if (!empty($data['posts_content'])) {
                        echo $data['posts_content'];
                    }
                    ?>
                </div>
                <div class="content-images-list">
                    <?php echo $this->contentImages($images, 'small', $content_link) ?>
                </div>
                <div class="invite-act">
                    <?php if($data['content_status'] == 0): ?>
                        <a href="javascript:void(0)" class="btn btn-primary ignore-flag" invite_id="<?php echo $data['content_invite_id'] ?>">忽略</a>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>
