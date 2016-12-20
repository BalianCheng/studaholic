<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * oauth.tpl.php
 */
?>
<div class="box">
    <form action="" method="post">
        <div class="box-body">
            <table class="table table-bordered">
                <tr>
                    <th>平台名称</th>
                    <th>
                        APP ID
                        <a href="javascript:void(0)" class="help-flag" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="新浪微博请填写APP Key">
                            <i class="fa fa-question-circle-o"></i>
                        </a>
                    </th>
                    <th>APP Key
                        <a href="javascript:void(0)" class="help-flag" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="新浪微博请填写APP Security">
                            <i class="fa fa-question-circle-o"></i>
                        </a>
                    </th>
                </tr>

                <?php foreach ($data['oauth'] as $name => $config) : ?>
                    <tr>
                        <td>
                            <div class="form-control-static">
                                <?php echo $data['oauth_config_name'][$name] ?>
                            </div>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="oauth[<?php echo $name ?>][app_id]"
                                   value="<?php echo $this->e($config, 'app_id') ?>">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="oauth[<?php echo $name ?>][app_key]"
                                   value="<?php echo $this->e($config, 'app_key') ?>">
                        </td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-primary" value="保存">
        </div>
    </form>
</div>

<script>
    $(function(){
        $('.help-flag').popover();
    })
</script>
