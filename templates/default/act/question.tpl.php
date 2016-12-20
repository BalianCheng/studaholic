<?php
/**
 * 提问
 *
 * @Auth: cmz <393418737@qq.com>
 * question.tpl.php
 */
//print_r($data);
$images = &$data['images'];
$relation_data = &$data['relation_data'];
$content_link = $this->url('content:question', array('question_id' => $data['relation_id']));
$act_user_home_link = $this->url('user:detail', array('account' => $data['account']));
?>
<div class="row">
    <div class="col-md-12">
        <div class="media">
            <div class="media-body">
                <div class="user-info">
                    <?php echo $this->a($data['nickname'], $act_user_home_link, array('class' => 'ia')) ?> 提问
                </div>
                <div class="media-heading">
                    <?php echo $this->a($data['title'], $content_link) ?>
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
