<?php
/**
 * 回答问题
 *
 * @Auth: cmz <393418737@qq.com>
 * question_answer.tpl.php
 */
$relation_data = $data['relation_data'];
$act_user_home_link = $this->url('user:detail', array('account' => $data['account']));
$answer_link = $this->url('content:question', array('question_id' => $relation_data['question_id']));
$answer_interact_link = $this->url('interact:answer', array(
        'question_id' => $relation_data['question_id'],
        'answer_id' => $relation_data['answer_id'])
);
?>
<div class="row">
    <div class="col-md-12">
        <div class="media">
            <div class="media-body">
                <div class="user-info">
                    <?php echo $this->a($data['nickname'], $act_user_home_link, array('class' => 'ia')) ?> 回答了问题
                </div>
                <div class="media-heading">
                    <?php echo $this->a($data['title'], $answer_link . "#answer_{$data['relation_id']}") ?>
                </div>
                <div class="content">
                    <a href="<?php echo $answer_interact_link ?>" class="ia">
                        <?php echo $relation_data['content'] ?>
                    </a>
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
