<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * question.tpl.php
 */
$images = &$data['images'];
$answer_up_count = $this->e($data, 'answer_up_count', 0);
$content_link = $this->url('content:question', array('question_id' => $data['question_id']));

if (!empty($data['answer_id'])) {
    $user_home_link = $this->url('user:detail', array('account' => $data['answer_account']));
    $user_link_title = $data['answer_nickname'];
    $user_avatar = $data['answer_avatar'];

    $user_nickname = $this->userNickname($data['answer_account'], $data['answer_nickname'], $data['answer_introduce'], true, array('class' => 'ia')) . ' 回答了该问题';
} else {
    $user_home_link = $this->url('user:detail', array('account' => $data['account']));
    $user_link_title = $data['nickname'];
    $user_avatar = $data['avatar'];

    $user_nickname = $this->userNickname($data['account'], $data['nickname'], $data['introduce'], true, array('class' => 'ia')) .
        $this->xsHideFTime($data['post_time'], ' 提出了问题于 ');
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="media">
            <div class="media-left tac hidden-xs">
                <div class="btn btn-count" title="赞同">
                    <?php echo $this->fCount($answer_up_count) ?>
                </div>
                <div title="赞同人数" class="left-praise-tips">赞</div>
            </div>
            <div class="media-body">
                <div class="media-heading">
                    <a href="<?php echo $content_link ?>">
                        <?php echo $data['title'] ?>
                    </a>
                </div>
                <div class="user-info">
                    <a title="<?php echo $user_link_title; ?>" href="<?php echo $user_home_link ?>">
                        <?php echo $this->userAvatar($user_avatar, '24px') ?>
                    </a>
                    <?php echo $user_nickname ?>
                </div>
                <div class="content">
                    <?php
                    if ($data['answer_id'] && !empty($data['answer_content'])) {
                        $answer_url = $this->url('interact:answer', array('question_id' => $data['question_id'], 'answer_id' => $data['answer_id']), array('class' => 'ia'));
                        printf('<a href="%s" class="ia">%s</a>', $answer_url, $data['answer_content']);
                    } elseif (!empty($data['question_content'])) {
                        echo $data['question_content'];
                    }
                    ?>
                </div>
                <div class="content-images-list">
                    <?php echo $this->contentImages($images, 'small', $content_link) ?>
                </div>
                <div class="stat-info hidden visible-xs">
                    <?php echo $this->fCount($answer_up_count) ?> 赞
                    <a href="<?php echo $content_link ?>"><i class="iconfont-small act-icon icon-comment"></i>添加评论</a>
                </div>
            </div>
        </div>
    </div>
</div>
