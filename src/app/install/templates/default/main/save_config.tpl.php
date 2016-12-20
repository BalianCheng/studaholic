<div class="container" style="margin-top:50px;text-align: center">
    <div class="row">
        <div class="col-lg-12 col-centered">
            正在保存数据库配置文件...
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        setTimeout(function(){
            $.post('<?php echo $this->url('test:dbFile') ?>', function (d) {
                if (d.status != 1) {
                    layer.msg(d.message);
                } else {
                    window.location.href = '<?php echo $this->url('main:import') ?>'
                }
            });
        }, 1000);
    });
</script>

<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * save_config.tpl.php
 */
