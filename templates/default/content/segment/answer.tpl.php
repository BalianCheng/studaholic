<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * answer.tpl.php
 */

$up_class = $down_class = '';
if ($data['stand'] == 1) {
    $up_class = 'act';
}

if ($data['stand'] == 2) {
    $down_class = 'act';
}

$reply_link = $this->url('interact:answer', array(
    'question' => $data['question_id'],
    'answer_id' => $data['answer_id']
));

?>
<div class="media answer-list" id="answer_<?php echo $data['answer_id'] ?>">
    <div class="media-left answer-act">
        <span class="btn btn-up act-flag <?php echo $up_class ?>" act="up"
              question-id="<?php echo $data['question_id'] ?>"
              answer-id="<?php echo $data['answer_id'] ?>">
            <i class="iconfont-small icon-up"></i>
            <span class="up-count-flag"
                  id="up-count-<?php echo $data['answer_id'] ?>"><?php echo $data['up_count'] ?></span>
        </span>
        <span class="btn btn-down act-flag <?php echo $down_class ?>" act="down"
              question-id="<?php echo $data['question_id'] ?>"
              answer-id="<?php echo $data['answer_id'] ?>">
            <i class="iconfont-small icon-down"></i>
        </span>
    </div>
    <div class="media-body answer-list-flag">
        <div class="media">
            <div class="media-body">
                <div class="user-info">
                    <?php echo $this->userNickname($data['account'], $data['nickname'], $data['introduce'], true, array('class' => 'ia')) ?>
                </div>
            </div>
            <div class="media-right">
                <a href="<?php echo $this->url('user:detail', array('account' => $data['account'])) ?>">
                    <?php echo $this->userAvatar($data['avatar'], '30px') ?>
                </a>
            </div>
        </div>
        <div class="answer-content content">
            <?php
            //被屏蔽
            if($data['status'] == -2) {
                echo $this->block('<该回答已经被屏蔽>', array('class' => 'blocked-content'));
            } else {
                echo $data['answer_content'];
            }
            ?>
        </div>
        <div class="answer-stat">
            <small>
                发布于 <?php echo $this->ftime($data['answer_time']) ?>
                <a href="<?php echo $reply_link ?>" class="ia" style="padding-left:10px">
                    <i class="iconfont-small act-icon icon-comment"></i><?php echo $data['comment_count'] ?>条评论
                </a>
                <span class="answer-control-panel">
                    <a href="javascript:void(0)" report-id="<?php echo $data['answer_id'] ?>" report-type="answer" class="ia report-flag">
                        <i class="iconfont-small act-icon icon-report"></i>举报
                    </a>
                </span>
            </small>
        </div>
    </div>
</div>

