<?php
/**
 * @Auth wonli <wonli@live.com>
 * info.tpl.php
 */
$style = 'height:128px;';
if (!empty($data['platformInfo']['avatar'])) {
    $style .= 'background: url(' . $data['platformInfo']['avatar'] . ');background-size:cover';
}
?>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-sm-8 col-centered">
            <form method="post" id="info-form" enctype="multipart/form-data">
                <div class="form-group">
                    <div class="img-circle" id="image-preview" style="<?php echo $style ?>">
                        <label for="image-upload" id="image-label"></label>
                        <input type="file" name="avatar" id="image-upload"/>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="account" name="account" placeholder="请输入您的帐号">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="nickname"
                           value="<?php echo $data['platformInfo']['nickname'] ?>" name="nickname" placeholder="昵称">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="introduce" name="introduce" placeholder="一句话介绍自己">
                </div>
                <div class="form-group">
                    <div id="validator-tips" class="validator-tips"></div>
                </div>
                <div class="form-group">
                    <button type="submit" id="submit_button" class="form-control btn btn-primary">确认创建</button>
                </div>
                <div class="form-group">
                    <a href="<?php echo $this->url('guide:platform_bind', $this->params) ?>" class="form-control btn btn-default">绑定已有帐号</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo $this->res('js/jquery.uploadPreview.min.js') ?>"></script>
<script src="<?php echo $this->res('libs/nice-validator/0.10.11/jquery.validator.min.js?local=zh-CN') ?>"></script>
<script type="text/javascript">
    $(function () {
        $.uploadPreview({
            input_field: "#image-upload",
            preview_box: "#image-preview",
            label_field: "#image-label",
            label_default: "",
            label_selected: "",
            no_label: false
        });

        $('#info-form').validator({
            stopOnError: true,
            fields: {
                'account': {
                    rule: "required;remote(<?php echo $this->url('action:checkAccount') ?>)",
                    tip: "请输入您的帐号"
                },
                'nickname': {
                    rule: "required;remote(<?php echo $this->url('action:checkNickname') ?>)",
                    tip: "请输入您的昵称"
                }
            },
            msgMaker: function (opt) {
                $('#validator-tips').html('<span class="' + opt.type + '">' + opt.msg + "</span>");
            }
        });

    });
</script>
