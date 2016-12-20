<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * login.tpl.php
 */
?>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-sm-8 col-centered">
            <form method="post" id="login-form" action="<?php echo $this->url('user:login', array('back' => $this->ee($data, 'back'))) ?>">
                <div class="form-group">
                    <input type="text" class="form-control" id="account" name="account" placeholder="账号">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="密码">
                </div>
                <div class="form-group">
                    <div id="validator-tips" class="validator-tips"></div>
                </div>
                <?php $this->loginButton($data['back']) ?>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo $this->res('libs/nice-validator/0.10.11/jquery.validator.min.js?local=zh-CN') ?>"></script>
<script>
    $(function () {
        $('#login-form').validator({
            rules: {
                account: [/^[a-zA-Z_0-9]+$/, '字母数字或下划线。']
            },
            msgMaker: function (opt) {
                $('#validator-tips').html('<span class="' + opt.type + '">' + opt.msg + "</span>");
            },
            fields: {
                "account": {
                    rule: "required;account)",
                    tip: "请输入您的账号"
                },
                "password": {
                    rule: "required;length(6~16)",
                    tip: "请输入您的密码"
                }
            }
        });
    })
</script>

