<?php
/**
 * @Auth wonli <wonli@live.com>
 * account.tpl.php
 */
$accountInfo = &$data['account_info'];
?>
<div class="col-md-12">
    <form class="form-horizontal" method="post">
        <?php if($accountInfo['from_platform'] != 1 && empty($accountInfo['password'])) : ?>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-3 form-control-static">
                    设置密码启用本地登录
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label tal">帐号</label>
                <div class="col-sm-3 form-control-static">
                    <?php echo $accountInfo['account'] ?>
                </div>
            </div>
        <?php else : ?>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label tal">原密码</label>
                <div class="col-sm-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="原密码">
                </div>
            </div>
        <?php endif ?>

        <div class="form-group">
            <label for="new_password" class="col-sm-2 control-label tal">新密码</label>
            <div class="col-sm-3">
                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="新密码">
            </div>
        </div>
        <div class="form-group">
            <label for="repeat_new_password" class="col-sm-2 control-label tal">重复新密码</label>
            <div class="col-sm-3">
                <input type="password" class="form-control" id="repeat_new_password" name="repeat_new_password"
                       placeholder="重复新密码">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">保存</button>
            </div>
        </div>
    </form>
</div>

