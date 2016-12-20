<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * reply.tpl.php
 */
//print_r($data);
?>
<div class="media reply-list" id="reply_<?php echo $data['reply_id'] ?>">
    <div class="media-left">
        <a href="<?php echo $this->url('user:detail', array('account' => $data['account'])) ?>">
            <?php echo $this->userAvatar($data['avatar'], '36px') ?>
        </a>
    </div>
    <div class="media-body">
        <div class="row reply-list-flag">
            <div class="col-md-12 content">
                <div class="user-info">
                    <?php echo $this->userNickname($data['account'], $data['nickname'], $data['introduce'], true, array('class' => 'ia')) ?>
                </div>
                <?php
                if ($data['status'] == -2) {
                    echo $this->block('<该回复已经被屏蔽>', array('class' => 'blocked-content'));
                } else {
                    echo $data['reply_content'];
                }
                ?>
            </div>
            <div class="col-md-12">
                <small style="color:#969696">
                    发布于 <?php echo $this->ftime($data['reply_time']) ?>
                    <a href="<?php echo $this->url('interact:reply', array('posts_id' => $data['posts_id'], 'reply_id' => $data['reply_id'])) ?>"
                       class="ia" style="padding-left:10px">
                        <i class="iconfont-small act-icon icon-comment"></i><?php echo $data['comment_count'] ?>条评论
                    </a>
                    <span class="reply-control-panel">
                        <a href="javascript:void(0)" class="reply-up-flag ia"
                           reply-id="<?php echo $data['reply_id'] ?>">
                            <?php
                            if ($data['is_up']) {
                                printf('<i class="iconfont-small act-icon icon-hand-down"></i>取消(%d)', $data['up_count']);
                            } else {
                                printf('<i class="iconfont-small act-icon icon-hand-up"></i>支持(%d)', $data['up_count']);
                            }
                            ?>
                        </a>
                        <a href="javascript:void(0)" style="margin-left:10px"
                           report-id="<?php echo $data['reply_id'] ?>" report-type="reply" class="report-flag ia">
                            <i class="iconfont-small act-icon icon-report"></i>举报
                        </a>
                    </span>
                </small>
            </div>
        </div>
    </div>
</div>
