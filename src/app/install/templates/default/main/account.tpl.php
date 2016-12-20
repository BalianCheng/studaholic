<div class="container" style="margin-top:50px">
    <div class="row">
        <div class="col-lg-12 col-centered">
            <form class="form-horizontal" id="account_form">
            <div class="form-group">
                    <label for="name" class="col-sm-4 control-label">用户名</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="name" id="name" placeholder="管理员用户名">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="col-sm-4 control-label">密码</label>
                    <div class="col-sm-8">
                        <input type="password" class="form-control" name="password" id="password" placeholder="密码">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-4 col-sm-8">
                        <button type="button" id="addAccount" class="btn btn-primary">提交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        var name = $('#name'), password = $('#password'), s = false, data = {};
        $('#addAccount').on('click', function () {
            if (name.val() != '' && password.val() != '') {
                s = true;
                data.name = name.val();
                data.password = password.val();
            } else if (name.val() == '') {
                layer.msg('用户名不能为空');
            } else if (password.val() == '') {
                layer.msg('密码不能为空');
            }

            if (s) {
                $.post('<?php echo $this->url('main:addAccount') ?>', data, function(d){
                    if (d.status != 1) {
                        layer.msg(d.message);
                    } else {
                        window.location.href = '<?php echo $this->url('main:end') ?>';
                    }
                })
            }
        })
    });
</script>

<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * account.tpl.php
 */
