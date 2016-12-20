<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * article.tpl.php
 */
$images = &$data['images'];
$topicInfo = &$data['topic_info'];
$content_link = $this->url('content:article', array('article_id' => $data['article_id']));
?>

<div class="row">
    <div class="col-md-12">
        <div class="content-list-title">
            <a href="<?php echo $content_link ?>"><?php echo $data['title'] ?></a>
        </div>
        <div class="content">
            <?php echo $data['article_summary'] ?>
        </div>
        <div class="user-info" style="margin-top:5px">
            发布于 <?php echo $this->fTime($data['post_time']) ?>
        </div>
    </div>
</div>




