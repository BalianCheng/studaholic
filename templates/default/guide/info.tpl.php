<div class="container">
    <div class="row">
        <div class="col-md-4 col-sm-8 col-centered">
            <form method="post" id="info-form" enctype="multipart/form-data">
                <div class="form-group">
                    <div class="img-circle" id="image-preview">
                        <label for="image-upload" id="image-label"></label>
                        <input type="file" name="avatar" id="image-upload"/>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="account"
                           value="<?php echo $this->data['loginUser']['account'] ?>" name="nickname" placeholder="昵称">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="account" name="introduce" placeholder="一句话介绍自己">
                </div>
                <div class="form-group">
                    <div id="validator-tips" class="validator-tips"></div>
                </div>
                <div class="form-center-button">
                    <button type="submit" id="submit_button" class="btn btn-default btn-current">完成</button>
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
            fields: {
                "nickname": {
                    rule: "required;remote(<?php echo $this->url('action:checkNicknameExcludeLoginUser') ?>)",
                    tip: "请输入您的昵称"
                }
            },
            msgMaker: function (opt) {
                $('#validator-tips').html('<span class="' + opt.type + '">' + opt.msg + "</span>");
            }
        });

    });
</script>
<?php
/**
 * @Auth wonli <wonli@live.com>
 * info.tpl.php
 */
