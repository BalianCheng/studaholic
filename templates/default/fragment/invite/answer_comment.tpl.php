<div class="answer-comment-list">
    <div class="media">
        <div class="media-left">
            <a href="<?php echo $this->url('user:detail', array('account' => $data['account'])) ?>">
                <?php echo $this->userAvatar($data['avatar']) ?>
            </a>
        </div>
        <div class="media-body">
            <div class="user-info">
                <?php echo $this->userNickname($data['account'], $data['nickname'], $data['introduce'], true, array('class' => 'ia',)) ?>
            </div>
            <div style="font-size:16px;">
                <?php echo $data['comment_content'] ?>
            </div>
            <div>
                <small>
                    回复于 <?php echo $this->ftime($data['comment_time']) ?>
                </small>
            </div>
        </div>
    </div>
</div>
<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * comment_list.tpl.php
 */

