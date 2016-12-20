<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * article.tpl.php
 */
$content = isset($data['content']) ? $data['content'] : '';
$editor_data = array('isLogin' => $data['isLogin'], 'loginUser' => $data['loginUser'], 'content' => '');
?>
<div class="form-group">
    <label for="current-content">
        上篇内容<a href="javascript:void(0)" class="label-action" id="content-fold" op="0">展开</a>
    </label>
    <div class="form-control-static content content-fold-flag">
        <?php echo $content ?>
    </div>
</div>
<div class="form-group">
    <label for="content">追加文章内容</label>
    <?php $this->renderTpl('fragment/editor/main_form', $editor_data) ?>
</div>
