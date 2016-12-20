<?php
/**
 * @Auth wonli <wonli@live.com>
 * chiefEditor.tpl.php
 */
$chiefEditor = !empty($data['chiefEditor'])?$data['chiefEditor']:array();
?>
<div class="box">
    <form action="" method="post">
        <div class="box-header with-border">
            <div class="box-title">话题主编</div>
        </div>
        <div class="box-body">
            <textarea class="form-control" name="editor_uid"
                      rows="3"><?php echo $this->e($chiefEditor, 'editor_uid') ?></textarea>
            <p style="margin-top:10px;">输入用户UID，以英文逗号分隔， 主编可以编辑所有话题内容。</p>
        </div>
        <div class="box-footer">
            <input type="submit" value="保存" class="btn btn-primary"/>
        </div>
    </form>
</div>
