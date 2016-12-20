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
                    <div class="img-circle" id="image-preview" style="<?php echo $style ?>"></div>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="account" name="account" placeholder="请输入已有帐号">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="密码">
                </div>
                <div class="form-group">
                    <div id="validator-tips" class="validator-tips"></div>
                </div>
                <div class="form-group">
                    <button type="submit" id="submit_button" class="form-control btn btn-primary">绑定帐号</button>
                </div>
                <div class="form-group">
                    <a href="<?php echo $this->url('guide:platform_register', $this->params) ?>"
                       class="form-control btn btn-default">创建新帐号</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo $this->res('libs/nice-validator/0.10.11/jquery.validator.min.js?local=zh-CN') ?>"></script>
<script type="text/javascript">
    $(function () {
        $('#info-form').validator({
            stopOnError: true,
            fields: {
                'account': {
                    rule: "required;remote(<?php echo $this->url('action:validateAccount') ?>)",
                    tip: "请输入您已有的帐号"
                },
                'password': {
                    rule: "required",
                    tip: '请输入您的密码'
                }
            },
            msgMaker: function (opt) {
                $('#validator-tips').html('<span class="' + opt.type + '">' + opt.msg + "</span>");
            },
            valid: function (form) {
                form.submit();
            }
        });

    });
</script>
