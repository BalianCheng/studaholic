<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * article.tpl.php
 */
$images = &$data['images'];
$topicInfo = &$data['topic_info'];
$content_link = $this->url('content:article', array('article_id' => $data['article_id']));
$author = $this->userNickname($data['account'], $data['nickname'], $data['introduce'], true, array('class' => 'ia'));
?>
<div class="row">
    <div class="col-md-12">
        <div class="media">
            <div class="media-body">
                <div class="content-list-title">
                    <a href="<?php echo $content_link ?>"><?php echo $data['title'] ?></a>
                </div>
            </div>

            <div class="media-right tac">
                <a title="<?php echo $data['nickname'] ?> "
                   href="<?php echo $this->url('user:detail', array('account' => $data['account'])) ?>">
                    <?php echo $this->userAvatar($data['avatar'], '42px') ?>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="content">
            <?php echo $data['article_summary'] ?>
        </div>
        <div class="user-info">
            <?php echo $author ?> 于 <?php echo $this->fTime($data['post_time']) ?>
        </div>
    </div>
</div>

