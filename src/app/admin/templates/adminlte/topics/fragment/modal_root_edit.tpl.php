<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * modal_root_edit.tpl.php
 */
$topic = &$data['topic'];
?>
<div class="modal-dialog" role="dialog">
    <form action="<?php echo $this->url('topics:saveRootTopic') ?>" id="rootTopicForm" method="post">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">编辑话题分类</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" name="topic_id" value="<?php echo $this->e($topic, 'topic_id', 0) ?>">
                    <label for="">分类名称(分类不能作为话题被搜索)</label>
                    <input class="form-control" name="topic_name" type="text" id="root_topic_name"
                           value="<?php echo $this->e($topic, 'topic_name') ?>">
                </div>

                <div class="form-group">
                    <label for="">话题访问地址(推荐使用英文字母或数字)</label>
                    <input class="form-control" name="topic_url" type="text" id="root_topic_url"
                           value="<?php echo $this->e($topic, 'topic_url') ?>">
                </div>

                <div class="form-group">
                    <label for="">设置为推荐分类</label>
                    <div style="clear:both">
                    <input type="checkbox" class="modal-toggle-flag" name="as_recommend" data-toggle="toggle" data-on="是"
                           data-off="否" <?php echo (!empty($topic['as_recommend'])) ? 'checked' : '' ?> data-size="small">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="submit" class="btn btn-primary">保存</button>
            </div>
        </div>
    </form>
</div>
