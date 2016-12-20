<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * article.tpl.php
 */
$content = isset($data['content']) ? $data['content'] : '';
$summary = !empty($data['summary']) ? $data['summary'] : '';
$editor_data = array('isLogin' => $data['isLogin'], 'loginUser' => $data['loginUser'], 'content' => $content);
$current_category_id = isset($data['category_id']) ? (int)$data['category_id'] : 0;
?>
<div class="form-group">
    <label for="category_select">文章分类</label>
    <select id="category_select" name="category_id" class="form-control"></select>
</div>
<div class="form-group">
    <label for="summary">摘要(留空则截取正文部分)</label>
    <textarea name="summary" class="form-control" id="summary" cols="30" rows="3"><?php echo $summary ?></textarea>
</div>
<div class="form-group">
    <label for="content">文章内容</label>
    <?php $this->renderTpl('fragment/editor/main_form', $editor_data) ?>
</div>
<script>
    $(function () {
        var current_category = <?php echo $current_category_id ?>;
        $.get('<?php echo $this->url('action:userArticleCategory') ?>', function (d) {
            var options = '', category_select = $('#category_select');
            for (var i = 0, j = d.data.length; i < j; i++) {
                options += '<option value="' + d.data[i]['category_id'] + '">' + d.data[i]['category_name'] + '</option>';
            }
            category_select.append(options);
        });
    })
</script>
