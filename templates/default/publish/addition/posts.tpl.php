<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * posts.tpl.php
 */
$content = isset($data['content'])?$data['content']:'';
$editor_data = array('isLogin' => $data['isLogin'], 'loginUser' => $data['loginUser'], 'content' => $content);
?>
<div class="form-group">
    <label for="content">帖子内容</label>
    <?php $this->renderTpl('fragment/editor/main_form', $editor_data) ?>
</div>
