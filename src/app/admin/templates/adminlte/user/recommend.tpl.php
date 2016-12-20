<div class="box box-primary">
    <form action="" method="post">
        <div class="box-header with-border">
            <div class="row">
                <div class="col-sm-5 col-md-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="addUid" name="uid" placeholder="请输入用户uid">
                        <span class="input-group-btn">
                          <button class="btn btn-primary btn-flat" id="addUidBtn" type="button">添加</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body table-responsive">
            <?php if (!empty($data['recommendUser'])) : ?>
                <table class="table table-bordered border-hover">
                    <tr>
                        <th>UID</th>
                        <th style="min-width:90px;">帐号</th>
                        <th style="min-width:100px;">昵称</th>
                        <th style="min-width:100px;">简介</th>
                        <th style="min-width:160px;">排序</th>
                        <th style="min-width:150px;">操作</th>
                    </tr>
                    <?php foreach ($data['recommendUser'] as $u) : ?>
                        <tr>
                            <td><?php echo $u['uid'] ?></td>
                            <td><?php echo $u['account'] ?></td>
                            <td><?php echo $u['nickname'] ?></td>
                            <td><?php echo $u['introduce'] ?></td>
                            <td>
                                <input type="text" class="form-control" name="info[<?php echo $u['recommend_id'] ?>][sort]" value="<?php echo $u['sort'] ?>"/>
                            </td>
                            <td>
                                <a href="javascript:void(0)" class="confirm-href-flag"
                                   action="<?php echo $this->url('user:delRecommendUser', array('id' => $u['recommend_id'])) ?>">删除</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <div style="margin-top:5px;">仅前十个生效，用于注册引导最后一步和个人动态少于一定条数时向用户推荐的用户。</div>
            <?php else : ?>
                <div>暂无推荐用户</div>
            <?php endif ?>
        </div>
        <div class="box-footer">
            <input type="submit" class="btn btn-primary" value="保存"/>
        </div>
    </form>
</div>
<script>
    $('#addUidBtn').on('click', function () {
        var uid = $('#addUid').val();
        if (!uid) {
            layer.msg('请输入要推荐的用户UID');
            return false;
        } else {
            $.post('<?php echo $this->url('user:addRecommendUser') ?>', {'uid': uid}, function (d) {
                if (d.status == 1) {
                    layer.msg('推荐成功');
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                } else {
                    layer.msg(d.message);
                }
            })
        }
    });
</script>
<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * recommend.tpl.php
 */
