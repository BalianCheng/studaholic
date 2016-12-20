<div class="container" style="margin-top:50px;">
    <form class="form-horizontal" action="<?php echo $this->url('main:saveConfig') ?>" id="db_form" method="post">
        <div class="form-group">
            <label for="db_host" class="col-sm-4 control-label">数据库主机</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="db_host"
                       value="<?php echo $this->e($data, 'db_host') ?>" id="db_host" placeholder="127.0.0.1">
            </div>
        </div>
        <div class="form-group">
            <label for="db_port" class="col-sm-4 control-label">数据库端口</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="db_port"
                       value="<?php echo $this->e($data, 'db_port') ?>" id="db_port" placeholder="3306">
            </div>
        </div>
        <div class="form-group">
            <label for="db_user" class="col-sm-4 control-label">数据库用户名</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="db_user"
                       value="<?php echo $this->e($data, 'db_user') ?>" id="db_user" placeholder="">
            </div>
        </div>
        <div class="form-group">
            <label for="db_pass" class="col-sm-4 control-label">数据库密码</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="db_pass"
                       value="<?php echo $this->e($data, 'db_pass') ?>" id="db_pass" placeholder="">
            </div>
        </div>
        <div class="form-group">
            <label for="db_name" class="col-sm-4 control-label">数据库名称</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="db_name"
                       value="<?php echo $this->e($data, 'db_name') ?>" id="db_name" placeholder="">
            </div>
        </div>
        <div class="form-group">
            <label for="db_prefix" class="col-sm-4 control-label">表前缀</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" name="db_prefix"
                       value="<?php echo $this->e($data, 'db_prefix') ?>" id="db_prefix" placeholder="">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <button type="submit" class="btn btn-primary">同意并开始安装</button>
                <a href="rules.html" target="_blank">用户协议</a>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(function () {
        var form = $('#db_form'), s = false;
        form.on('submit', function () {
            var prefix = $('#db_prefix').val(), reg = /^[A-Za-z0-9_]{1,12}$/;
            if (prefix && !reg.test(prefix)) {
                layer.msg('请输入12个字符以内的字母、数字或下划线');
                return false;
            }

            $.ajax({
                'url': '<?php echo $this->link('test:db') ?>',
                'type': 'post',
                'data': form.serializeArray(),
                'async': false,
                'success': function (d) {
                    if (d.status == 1) {
                        s = true;
                    } else {
                        layer.msg(d.message);
                    }
                }
            });

            return s;
        })
    });
</script>
