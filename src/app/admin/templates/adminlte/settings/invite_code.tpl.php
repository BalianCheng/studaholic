<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * invite_code.tpl.php
 */
if (!empty($data['inviteCodeList'])) {
    foreach ($data['inviteCodeList'] as $invite) {
        $code[] = $invite['invite_code'];
    }
}

function ef($data, $key, $content) {
    if(!empty($data[$key])) {
        echo $content;
    }
}

$statusDOM = array(
    0 => '<span style="color:orangered">过期</span>',
    1 => '<span style="color:forestgreen">生效</span>',
)
?>
<div class="box table-responsive">
    <form action="" method="post">
        <div class="box-header with-border">
            <h3 class="box-title">邀请码列表</h3>
        </div>
        <div class="box-body">
            <?php if (!empty($data['inviteCodeList'])) : ?>
                <table class="table table-bordered table-hover">
                    <tr>
                        <th style="width:40px;min-width:40px;">ID</th>
                        <th style="width:80px;min-width:80px;">邀请码</th>
                        <th style="width:100px;min-width:100px;">被使用次数</th>
                        <th style="width:60px;min-width:60px;">状态</th>
                        <th style="min-width:260px;">备注</th>
                        <th style="width:180px;min-width:180px;">创建日期</th>
                        <th style="width:160px;min-width:160px;">操作</th>
                    </tr>
                    <?php foreach ($data['inviteCodeList'] as $invite) : ?>
                        <tr>
                            <td><?php echo $invite['id'] ?></td>
                            <td><?php echo $invite['invite_code'] ?></td>
                            <td><?php echo $invite['use_count'] ?></td>
                            <td>
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="<?php echo $invite['id'] ?>[status]"
                                           data-toggle="toggle" data-on="生效" data-off="无效" data-width="60"
                                           data-size="small" <?php ef($invite, 'status', 'checked') ?>>
                                </label>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="<?php echo $invite['id'] ?>[comments]"
                                       value="<?php echo $invite['comments'] ?>">
                            </td>
                            <td><?php echo date('Y-m-d H:i:s', $invite['create_time']) ?></td>
                            <td>
                                <a href="<?php echo $this->url('settings:changeInviteCodeStatus', array('id' => $invite['id'])) ?>">
                                    切换状态
                                </a>
                                <a href="javascript:void(0)" class="confirm-href-flag"
                                   action="<?php echo $this->url('settings:delInviteCode', array('id' => $invite['id'])) ?>">删除</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </table>
            <?php else : ?>
                <div>暂无邀请码</div>
            <?php endif ?>
        </div>
        <div class="box-footer">
            <input type="button" class="btn btn-success" data-toggle="modal" data-target="#addInviteCode" value="添加邀请码">
            <input type="submit" class="btn btn-primary" value="保存设置">
        </div>
    </form>
</div>

<div class="modal fade" id="addInviteCode" tabindex="-1" role="dialog" aria-labelledby="inviteModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">邀请码</label>
                        <input type="text" class="form-control" name="inviteCode" id="inviteCode"
                               value="<?php echo \Cross\Core\Helper::random(5) ?>">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="control-label">备注</label>
                        <textarea class="form-control" name="comments" id="comments"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" id="addInviteBtn" class="btn btn-primary">添加</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#addInviteBtn').on('click', function () {
            var code = $('#inviteCode').val(), comments = $('#comments').val();
            if (!code) {
                layer.msg('请输入邀请码');
                return false;
            }

            if (comments.length > 30) {
                layer.msg('备注太长了!');
                return false;
            }

            $.post('<?php echo $this->url('settings:addInviteCode') ?>', {
                'invite_code': code,
                'comments': comments
            }, function (d) {
                if (d.status == 0) {
                    layer.msg(d.message);
                } else {
                    layer.msg('添加邀请码成功!');
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000)
                }
            })
        })
    })
</script>
