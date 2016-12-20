<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * comment.tpl.php
 */
//print_r($data);
?>
<div class="media comment-list">
    <div class="media-left">
        <a href="<?php echo $this->url('user:detail', array('account' => $data['account'])) ?>">
            <?php echo $this->userAvatar($data['avatar'], '36px') ?>
        </a>
    </div>
    <div class="media-body">
        <div class="row comment-list-flag">
            <div class="col-md-12 content">
                <div class="user-info">
                    <?php echo $this->userNickname($data['account'], $data['nickname'], $data['introduce'], true, array('class' => 'ia')) ?>
                </div>
                <?php
                if ($data['status'] == -2) {
                    echo $this->block('<该回复已经被屏蔽>', array('class' => 'blocked-content'));
                } else {
                    echo $data['comment_content'];
                }
                ?>
            </div>
            <div class="col-md-12" style="margin-bottom:6px;">
                <small style="color:#969696">
                    <?php echo $this->ftime($data['comment_time']) ?>
                    <span class="reply-control-panel">
                        <a href="javascript:void(0)" style="margin-left:5px" report-id="<?php echo $data['comment_id'] ?>" report-type="comment" class="report-flag ia">
                            <i class="iconfont-small act-icon icon-report"></i>举报
                        </a>
                    </span>
                </small>
            </div>
        </div>
    </div>
</div>
