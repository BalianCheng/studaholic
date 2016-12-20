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
            <div class="media-left tac hidden-xs">
                <div class="btn btn-count" title="回复">
                    <?php echo $this->fCount($interact_count) ?>
                </div>
                <div title="回复人数" class="left-praise-tips">回</div>
            </div>
            <div class="media-body">
                <div class="media-heading">
                    <a href="<?php echo $content_link ?>"><?php echo $data['title'] ?></a>
                </div>
                <div class="user-info">
                    <a title="<?php echo $data['nickname']; ?>"
                       href="<?php echo $this->url('user:detail', array('account' => $data['account'])) ?>">
                        <?php echo $this->userAvatar($data['avatar'], '24px') ?>
                    </a>
                    <?php
                    echo $this->userNickname($data['account'], $data['nickname'], $data['introduce'], true, array('class' => 'ia')) .
                        $this->xsHideFTime($data['post_time'], ' 发布了帖子于 ');
                    ?>
                </div>
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
                <div class="stat-info hidden visible-xs">
                    <?php echo $this->fCount($interact_count) ?> 回
                    <a  href="<?php echo $content_link ?>"><i class="iconfont-small act-icon icon-comment"></i>添加评论</a>
                </div>
            </div>
        </div>
    </div>
</div>

