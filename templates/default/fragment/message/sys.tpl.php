<?php
/**
 * @Auth wonli <wonli@live.com>
 * sys.tpl.php
 */
//print_r($data);
?>
<div id="message-area-<?php echo $data['id'] ?>">
    <div class="panel panel-message-list">
        <div class="panel-heading">
            <?php echo $data['content'] ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-8">
                    <div class="media">
                        <div class="media-left">
                            <span class="badge">社区管理员</span>
                        </div>
                        <div class="media-body">
                             发送时间：<?php echo $this->ftime($data['send_time']) ?>
                        </div>
                    </div>
                </div>

                <div class="col-xs-2">
                    <?php
                    if ($data['read_time'] == 0) {
                        echo '<span style="color:#f56955">未读</span>';
                    } else {
                        echo '已读';
                    }
                    ?>
                </div>
                <div class="col-xs-2">
                    <a href="javascript:void(0)" message-id="<?php echo $data['id'] ?>" class="ia del-message-id">删除</a>
                </div>
            </div>
        </div>
    </div>
</div>

