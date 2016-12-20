<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * question.tpl.php
 */
$images = &$data['images'];
$content_link = $this->url('content:question', array('question_id' => $data['question_id']));

if ($data['answer_id']) {
    $user = $this->userNickname($data['answer_account'], $data['answer_nickname'], $data['answer_introduce'], true, array('class' => 'ia'));
    $act_tips = ' 回答了该问题';
    $content = &$data['answer_content'];
    $user_avatar = $this->userAvatar($data['answer_avatar']);
    $user_home = $this->url('user:detail', array('account' => $data['answer_account']));

    $answer_link = $this->url('interact:answer', array('question_id' => $data['question_id'], 'answer_id' => $data['answer_id']));

} else {
    $user = $this->userNickname($data['account'], $data['nickname'], $data['introduce'], true, array('class' => 'ia'));
    $act_tips = ' 提出了该问题';
    $content = &$data['question_content'];
    $user_avatar = $this->userAvatar($data['avatar']);
    $user_home = $this->url('user:detail', array('account' => $data['account']));

    $answer_link = $this->url('content:question', array('question_id' => $data['question_id']));
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="media">
            <div class="media-left hidden-xs tac">
                <span class="btn btn-count">
                    <?php echo (int)$data['answer_up_count'] ?>
                </span>
                赞同
            </div>
            <div class="media-body">
                <div class="media">
                    <div class="media-body">
                        <div class="row">
                            <div class="col-md-12 user-info">
                                <?php echo $user . $act_tips ?>
                            </div>

                            <div class="col-md-12 content-list-title">
                                <a href="<?php echo $content_link ?>">
                                    <?php echo $data['title'] ?>
                                </a>
                            </div>

                            <div class="col-md-12 content">
                                <a href="<?php echo $answer_link ?>" class="ia">
                                    <?php echo $content ?>
                                </a>
                            </div>

                            <div class="col-md-12">
                                <div class="visible-xs">
                                    <small>
                                        <?php echo (int)$data['answer_up_count'] ?> 赞同
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="media-right">
                        <a href="<?php echo $user_home ?>">
                            <?php echo $user_avatar ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

