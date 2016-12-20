<?php
/**
 * @Auth wonli <wonli@live.com>
 * avatar.tpl.php
 */
$info = &$data['info'];
$platform = &$data['platform'];
$localAvatar = $this->resAbsoluteUrl($info['avatar']);
$style = 'height:128px;';
if (!empty($info['avatar'])) {
    $style .= 'background: url(' . $localAvatar . ');background-size:cover';
}

$avatarList = array($localAvatar);
if ($platform) {
    foreach ($platform as $p) {
        if (!empty($p['avatar']) && $p['avatar'] != $localAvatar) {
            $avatarList[] = $p['avatar'];
        }
    }
}
?>
<div class="col-md-12">
    <form class="form-horizontal" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <div class="img-circle" id="image-preview" style="<?php echo $style ?>">
                <label for="image-upload" id="image-label"></label>
                <input type="file" name="avatar" id="image-upload"/>
            </div>
            <input type="hidden" name="avatarSrc" id="avatarSrc">
        </div>
        <div class="form-group">
            <div class="col-md-12 tac">
                <button type="submit" class="btn btn-primary">保存</button>
                <button type="button" id="changeAvatar" style="display: none" class="btn btn-info">换一换</button>
            </div>
        </div>
    </form>
</div>
<script src="<?php echo $this->res('js/jquery.uploadPreview.min.js') ?>"></script>
<script type="text/javascript">
    var avatarList = <?php echo json_encode($avatarList) ?>, index = 0;
    $(function () {
        $.uploadPreview({
            input_field: "#image-upload",
            preview_box: "#image-preview",
            label_field: "#image-label",
            label_default: "",
            label_selected: "",
            no_label: false
        });

        var changeAvatar = $('#changeAvatar'), avatarListLength = avatarList.length;
        if (avatarListLength > 1) {
            changeAvatar.show().on('click', function () {
                for (var i = 0; i < avatarListLength; i++) {
                    if (index == avatarListLength - 1) {
                        index = 0;
                        break;
                    } else if (i > index) {
                        index = i;
                        break;
                    }
                }
                var avatar = avatarList[index];
                $('#avatarSrc').val(avatar);
                $('#image-preview').css({'background': 'url(' + avatar + ')', 'background-size': 'cover'});
            });
        }
    });
</script>
