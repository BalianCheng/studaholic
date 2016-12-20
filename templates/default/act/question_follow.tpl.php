<?php
/**
 * 关注问题
 *
 * @Auth: cmz <393418737@qq.com>
 * question_follow.php
 */
//print_r($data);
$act_user_home_link = $this->url('user:detail', array('account' => $data['account']));
?>
<div class="row">
    <div class="col-md-12">
        <div class="media">
            <div class="media-body">
                <div class="user-info">
                    <?php echo $this->a($data['nickname'], $act_user_home_link, array('class' => 'ia')) ?> 关注了问题
                </div>
                <div class="media-heading">
                    <?php echo $this->a($data['title'], $this->url('content:question', array('question_id' => $data['relation_id']))) ?>
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

