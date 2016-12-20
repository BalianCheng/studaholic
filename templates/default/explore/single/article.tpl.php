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
        <div class="row">
            <div class="col-xs-6">
                <div class="media">
                    <div class="media-left tac">
                        <a title="<?php echo $data['nickname'] ?>"
                           href="<?php echo $this->url('user:detail', array('account' => $data['account'])) ?>">
                            <?php echo $this->userAvatar($data['avatar'], '36px') ?>
                        </a>
                    </div>
                    <div class="media-body">
                        <div class="user-info">
                            <?php echo $this->userNickname($data['account'], $data['nickname'], '', true, array('class' => 'ia')) ?>
                            <div style="margin:5px 0;">
                                <?php echo $data['introduce']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-6 tar hidden-xs article-topic-list">
                <?php
                if(!empty($topicInfo)) {
                    $this->renderTpl('fragment/topic/list', $topicInfo);
                }
                ?>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="content-images-list">
            <?php echo $this->images($images, 'cover', $content_link) ?>
        </div>

        <div class="article-modal-title">
            <a href="<?php echo $content_link ?>"><?php echo $data['title'] ?></a>
        </div>

        <div class="content">
            <?php echo $data['article_summary'] ?>
        </div>
    </div>

    <div class="col-md-12 tar">
        <h5>
            <a href="<?php echo $content_link ?>" class="ia">
                浏览(<?php echo $data['article_hits'] ?>)
            </a>

            <a href="<?php echo $content_link ?>" class="ia">
                赞(<?php echo $data['up_count'] ?>)
            </a>
        </h5>
    </div>
</div>

