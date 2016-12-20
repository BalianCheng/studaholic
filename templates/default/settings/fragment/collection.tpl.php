<?php
/**
 * @Auth wonli <wonli@live.com>
 * avatar.tpl.php
 */
$info = &$data['info'];
$style = 'height:100%;';
if (!empty($info['qr'])) {
    $style .= 'background: url(' . $this->res($info['qr']) . ');background-size:cover';
}
?>
<div class="col-md-12 tac">
    <form class="form-horizontal" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <div class="img-thumbnail" id="qr-image-preview">
                <label for="qr-image-upload" id="qr-image-label" style="<?php echo $style ?>"></label>
                <input type="file" name="qr" id="qr-image-upload"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 tac">
                <button type="submit" class="btn btn-primary">保存</button>
            </div>
        </div>
    </form>
</div>
<script src="<?php echo $this->res('js/jquery.uploadPreview.min.js') ?>"></script>
<script type="text/javascript">
    $(function () {
        $.uploadPreview({
            input_field: "#qr-image-upload",
            preview_box: "#qr-image-preview",
            label_field: "#qr-image-label",
            label_default: "",
            label_selected: "",
            no_label: false
        });
    });
</script>
