<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * register.tpl.php
 */
$token = &$data['token'];
$loginUserInfo = &$data['loginUser'];
$inviteUserInfo = &$data['inviteUserInfo'];
$preview = (bool)$loginUserInfo['uid'] == $inviteUserInfo['uid'];

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
?>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-sm-8 col-centered">
            <?php echo $this->userAvatar($inviteUserInfo['avatar'], '96px') ?>
            <h3><?php echo $inviteUserInfo['nickname'] ?></h3>
            <p>
                <?php echo $this->e($inviteUserInfo, 'introduce') ?>
            </p>
        </div>

        <div class="col-md-4 col-sm-8 col-centered">
            <p>
                <?php
                if ($preview) {
                    echo '请将此页发给你想邀请的人';
                } else {
                    echo '你的朋友 ' . $inviteUserInfo['nickname'] . ' 邀请你加入我们!';
                }
                ?>
            </p>
        </div>

        <div class="row">
            <div class="col-md-4 col-sm-8 col-centered">
                <form method="post" id="register-form"
                      action="<?php echo $this->url('user:invite', array('token' => $token)) ?>">
                    <div class="form-group">
                        <input type="text" class="form-control" id="account" name="account" placeholder="账号">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="密码">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="repeat_password" name="repeat_password"
                               placeholder="再次输入密码">
                    </div>

                    <?php if ($preview) : ?>
                        <div class="form-group">
                            <button type="button" id="invite_register_tips" class="form-control btn btn-primary btn-current">注册
                            </button>
                        </div>
                    <?php else : ?>
                        <div class="form-group">
                            <button type="submit" id="submit_button" class="form-control btn btn-primary btn-current">注册</button>
                        </div>
                    <?php endif ?>

                    <div class="form-center-button">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="ag"> 已阅读并同意
                                <a href="javascript:void(0)" id="userAgreement" style="text-decoration:none">用户协议</a>
                            </label>
                        </div>
                    </div>
                    <div id="validator-tips" class="validator-tips"></div>
                </form>
            </div>
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
<script type="text/javascript" src="<?php echo $this->res('libs/layer/2.2/layer.js') ?>"></script>
<script
    src="<?php echo $this->res('libs/nice-validator/0.10.11/jquery.validator.min.js?local=zh-CN') ?>"></script>
<script>
    $(function () {
        $('#invite_register_tips').on('click', function () {
            layer.msg('请把此页发给你想邀请的人!');
        });

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
