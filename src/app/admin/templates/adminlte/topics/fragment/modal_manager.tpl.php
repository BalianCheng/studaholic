<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * modal_manager.tpl.php
 */
$editor = &$data['editor']['editor_uid'];
?>
<form id="topicEditorForm" action="<?php echo $this->url('topics:saveManager') ?>" method="post"
      enctype="multipart/form-data">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="topic_id" value="<?php echo $this->e($data, 'topic_id') ?>">
                <div class="form-group">
                    <label for="message-text" class="control-label">话题编辑的用户UID列表(用英文逗号分隔)</label>
                    <textarea class="form-control" name="editor_list" style="height:90px;"
                              id="editor_list"><?php echo $editor ?></textarea>
                </div>
                <div style="margin-top:5px;">话题编辑仅限于编辑当前话题下的内容</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" id="saveEditorButton" class="btn btn-primary">保存</button>
            </div>
        </div>
    </div>
</form>
