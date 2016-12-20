<?php
/**
 * @Auth wonli <wonli@live.com>
 * dialog.tpl.php
 */
//print_r($data);
$user_home_url = $this->url('user:detail', array('account' => $data['account']));
$user_home_link = $this->a($data['nickname'], $user_home_url, array('class' => 'ia'));
?>
<div id="dialog-area-<?php echo $data['receiver_uid'] ?>">

    <div class="panel panel-message-list">

        <div class="panel-heading">
            <a onclick="message(<?php echo $data['receiver_uid'] ?>)" href="javascript:void(0)">
                <?php echo $data['content'] ?>
            </a>
        </div>

        <div class="panel-body">
            <div class="row" style="padding-top:8px;">
                <div class="col-xs-6">
                    <div class="media">
                        <div class="media-left">
                            <a href="<?php echo $user_home_url ?>">
                                <?php echo $this->userAvatar($data['avatar'], '24px') ?>
                            </a>
                        </div>
                        <div class="media-body">
                            <?php echo $user_home_link ?>
                            <br />
                            <?php echo $this->ftime($data['send_time']) ?>
                        </div>
                    </div>
                </div>

                <div class="col-xs-3">
                    <?php
                    if ($data['read_time'] == 0) {
                        echo '<span style="color:#f56955">未读</span>';
                    } else {
                        echo '已读';
                    }
                    ?>
                </div>
                <div class="col-xs-3">
                    <a href="javascript:void(0)" dialog-id="<?php echo $data['receiver_uid'] ?>" class="ia del-message-dialog">删除</a>
                </div>
            </div>
        </div>

    </div>

</div>

