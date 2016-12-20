<div class="modal-dialog" role="dialog">
    <form action="<?php echo $this->url('content:blockContent') ?>" id="blockForm" method="post">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">内容屏蔽</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <?php if ($data['status'] == 1) : ?>
                        <div id="reasonArea">
                            <label for="">用户消息</label>
                            <textarea name="reason" id="blockTxt" class="form-control" cols="10" rows="3">与本站主题不符</textarea>
                        </div>
                    <?php else : ?>
                        <div id="unblockArea">
                            是否取消屏蔽
                        </div>
                    <?php endif ?>
                    <input name="title_id" type="hidden" value="<?php echo $data['title_id'] ?>"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary" id="blockButton">确定</button>
            </div>
        </div>
    </form>
</div>
<?php
/**
 * @Auth wonli <wonli@live.com>
 * block.tpl.php
 */
