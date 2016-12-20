<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * register.tpl.php
 */
$fields_config = array(
    'account' => array(
        'rule' => 'required;account;remote(' . $this->url('action:checkAccount') . ')',
        'tip' => '英文字母数字或下划线'
    ),
    'password' => array(
        'rule' => 'required;length(6~16)',
        'tip' => '请设置您的密码（6-16个字符）'
    ),
    'repeat_password' => array(
        'rule' => 'required;match(password)',
        'tip' => '请再输入一次密码',
    ),
    'ag' => array(
        'rule' => 'checked',
        'msg' => array(
            'checked' => '请仔细阅读用户协议',
        )
    )
);

$invite = &$data['invite'];
if ($invite) {
    $fields_config['invite_code'] = array(
        'rule' => 'required;remote(' . $this->url('action:checkInviteCode') . ')',
        'tip' => '请输入您的邀请码',
    );
}

?>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-sm-8 col-centered">
            <form method="post" id="register-form" autocomplete="off"
                  action="<?php echo $this->url('user:register', array('back' => $this->ee($data, 'back'))) ?>">
                <div class="form-group">
                    <input type="text" class="form-control" id="account" name="account" placeholder="账号"
                           autocomplete="off">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="密码">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="repeat_password" name="repeat_password"
                           placeholder="再次输入密码">
                </div>
                <?php if ($invite) : ?>
                    <div class="form-group">
                        <input type="text" class="form-control" id="invite_code" name="invite_code"
                               placeholder="请输入您的邀请码">
                    </div>
                <?php endif ?>

                <div id="validator-tips" class="validator-tips"></div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="ag"> 已阅读并同意
                    </label><a href="javascript:void(0)" id="userAgreement" style="text-decoration:none">用户协议</a>
                </div>

                <?php echo $this->registerButton($data['back']) ?>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" style="text-align: left" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">用户协议</h4>
            </div>
            <div class="modal-body" id="agreementContent"></div>
        </div>
    </div>
</div>
<script src="<?php echo $this->res('libs/nice-validator/0.10.11/jquery.validator.min.js?local=zh-CN') ?>"></script>
<script>
    $(function () {
        $('#register-form').validator({
            rules: {
                account: [/^[a-zA-Z_0-9]+$/, '账号仅支持字母，数字和下划线。']
            },
            msgMaker: function (opt) {
                $('#validator-tips').html('<span class="' + opt.type + '">' + opt.msg + "</span>");
            },
            fields: <?php echo json_encode($fields_config) ?>
        });

        $('#userAgreement').on('click', function () {
            $.get('<?php echo $this->url('action:getAgreement') ?>', function (d) {
                $('#agreementContent').html(d);
                $('#myModal').modal();
            })
        });
    })
</script>
