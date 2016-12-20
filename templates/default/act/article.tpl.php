<?php
/**
 * 发表文章
 *
 * @Auth: cmz <393418737@qq.com>
 * article.tpl.php
 */
$images = &$data['images'];
$relation_data = &$data['relation_data'];
$act_user_home_link = $this->url('user:detail', array('account' => $data['account']));
$content_link = $this->url('content:article', array('article_id' => $data['relation_id']));
?>
<div class="row">
    <div class="col-md-12">
        <div class="media">
            <div class="media-body">
                <div class="user-info">
                    <?php echo $this->a($data['nickname'], $act_user_home_link, array('class' => 'ia')) ?> 发表了文章
                </div>
                <div class="media-heading">
                    <?php echo $this->a($data['title'], $content_link) ?>
                </div>
                <div class="content">
                    <?php echo $relation_data['summary'] ?>
                </div>
                <div class="content-images-list">
                    <?php echo $this->contentImages($images, 'small', $content_link) ?>
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
