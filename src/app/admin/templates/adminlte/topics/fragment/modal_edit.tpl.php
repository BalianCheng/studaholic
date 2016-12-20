<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * modal_edit.tpl.php
 */
$id = $this->e($data, 'id', 0);
$pid = $this->e($data, 'pid', 0);
$topic = isset($data['topic'])?$data['topic']:array();

if(isset($topic['topic_image'])) {
    $topic_image = $this->getResource($this->e($topic, 'topic_image'));
    $style = "background:url('{$topic_image}') center center;background-size:cover;";
    $image_label_style = '';
} else {
    $style= '';
    $topic_image = $this->getResource('images/topic.png');
    $image_label_style = "background:url('{$topic_image}') no-repeat 50% 50%";
}

$params = array(
    'class' => 'form-control',
    'name' => 'parent_id'
);
function ef($data, $key, $content) {
    if(!empty($data[$key])) {
        echo $content;
    }
}
?>
<form id="topicSaveForm" action="<?php echo $this->url('topics:saveTopic') ?>" method="post" enctype="multipart/form-data">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="topic_id" value="<?php echo $id ?>">
                <div class="form-group">
                    <div id="image-preview" style="<?php echo $style ?>">
                        <label for="image-upload" id="image-label" style="<?php echo $image_label_style ?>"></label>
                        <input type="file" name="topic_image" id="image-upload"/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="">话题类型</label>
                    <div class="row">
                        <div class="col-xs-5">
                            <?php echo $this->select($data['root'], $pid, $params); ?>
                        </div>

                        <div class="col-xs-4">
                            <label class="checkbox-inline">
                                <input type="checkbox" class="modal-toggle-flag" name="as_recommend"
                                       data-toggle="toggle" data-on="推荐话题" data-off="普通话题" data-width="80"
                                       data-size="small" <?php ef($topic, 'as_recommend', 'checked') ?>>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="">话题名称</label>
                    <input class="form-control" name="topic_name" type="text" id="topic_name"
                           value="<?php echo $this->e($topic, 'topic_name') ?>">
                </div>

                <div class="form-group">
                    <label for="">话题访问地址(推荐使用英文字母或数字)</label>
                    <input class="form-control" name="topic_url" type="text" id="topic_url"
                           value="<?php echo $this->e($topic, 'topic_url') ?>">
                </div>

                <div class="form-group">
                    <input type="checkbox" class="modal-toggle-flag" name="enable_question"
                           data-toggle="toggle" data-on="开启问答" data-off="关闭问答" data-width="80"
                           data-size="small" <?php ef($topic, 'enable_question', 'checked') ?>>

                    <input type="checkbox" class="modal-toggle-flag" name="enable_posts"
                           data-toggle="toggle" data-on="开启讨论" data-off="关闭讨论" data-width="80"
                           data-size="small" <?php ef($topic, 'enable_posts', 'checked') ?>>

                    <input type="checkbox" class="modal-toggle-flag" name="enable_article"
                           data-toggle="toggle" data-on="开启文章" data-off="关闭文章" data-width="80"
                           data-size="small" <?php ef($topic, 'enable_article', 'checked') ?>>
                </div>

                <div class="form-group">
                    <label for="message-text" class="control-label">话题描述</label>
                    <textarea class="form-control" name="topic_description" style="height:120px;"
                              id="topic_description"><?php echo $this->e($topic, 'topic_description') ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="submit" class="btn btn-primary">保存</button>
            </div>
        </div>
    </div>
</form>
