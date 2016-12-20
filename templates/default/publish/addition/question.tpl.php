<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * question.tpl.php
 */
$content = isset($data['question_content'])?$data['question_content']:'';
$editor_data = array('isLogin' => $data['isLogin'], 'loginUser' => $data['loginUser'], 'content' => $content);
?>
<div class="form-group">
    <label for="content">问题补充（可选）</label>
    <?php $this->renderTpl('fragment/editor/main_form', $editor_data) ?>
</div>
