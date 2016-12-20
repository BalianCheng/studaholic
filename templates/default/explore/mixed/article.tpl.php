<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * article.tpl.php
 */
$images = &$data['images'];
$content_link = $this->url('content:article', array('article_id' => $data['article_id']));
?>
<div class="row">
    <div class="col-md-12">
        <div class="media">
            <div class="media-left tac hidden-xs">
                <div class="btn btn-count" title="点赞">
                    <?php echo $this->fCount($data['up_count']) ?>
                </div>
                <div title="点赞人数" class="left-praise-tips">赞</div>
            </div>
            <div class="media-body">
                <div class="media-heading">
                    <a href="<?php echo $content_link ?>"><?php echo $data['title'] ?></a>
                </div>
                <div class="user-info">
                    <a title="<?php echo $data['nickname'] ?> "
                       href="<?php echo $this->url('user:detail', array('account' => $data['account'])) ?>">
                        <?php echo $this->userAvatar($data['avatar'], '24px') ?>
                    </a>
                    <?php
                    echo $this->userNickname($data['account'], $data['nickname'], $data['introduce'], true, array('class' => 'ia')) .
                        $this->xsHideFTime($data['post_time'], ' 发布了文章于 ');
                    ?>
                </div>
                <div class="content">
                    <?php echo $data['article_summary'] ?>
                </div>
                <div class="content-images-list">
                    <?php echo $this->contentImages($images, 'small', $content_link) ?>
                </div>
                <div class="stat-info hidden visible-xs">
                    <?php echo $this->fCount($data['up_count']) ?> 赞
                    <a  href="<?php echo $content_link ?>"><i class="iconfont-small act-icon icon-comment"></i>添加评论</a>
                </div>
            </div>
        </div>
    </div>
</div>

