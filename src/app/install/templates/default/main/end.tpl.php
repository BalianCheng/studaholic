<div class="container" style="margin-top:50px;text-align: center">
    <div class="row">
        <div class="col-lg-12 col-centered" id="tips">
            点击锁定安装以继续
        </div>
        <div class="col-lg-12 col-centered" id="link" style="display: none">
            <a href="<?php echo $data['home_url'] ?>" target="_blank" style="color:#555">前台首页</a>
            <a href="<?php echo $data['admin_url'] ?>" target="_blank" style="color:#555">后台登录</a>
        </div>
    </div>
    <div class="row" style="padding:30px 0;">
        <div class="col-lg-12 col-centered">
            <a href="javascript:void(0)" id="lock" class="btn btn-lg btn-info" style="background:#00a65a;border-color:#00a65a">锁定安装</a>
            <a href="<?php echo $this->url() ?>" class="btn btn-lg btn-default">再次安装</a>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#lock').on('click', function () {
            var i = layer.confirm('锁定后再次安装需要删install.lock文件', {
                icon: 4,
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.post('<?php echo $this->url('main:lock') ?>', function (d) {
                    if (d == 1) {
                        $('#tips').hide();
                        $("#link").show();
                        layer.msg('锁定成功');
                    } else {
                        layer.msg('锁定失败，请检查目录权限');
                    }
                    layer.close(i);
                });
            }, function () {
            });
        });
    })
</script>
<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * end.tpl.php
 */
