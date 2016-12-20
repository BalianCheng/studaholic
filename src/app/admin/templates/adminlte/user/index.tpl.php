<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * index.tpl.php
 */

$clean_search = isset($data['t']);
$select_params = array(
    'name' => 't',
    'class' => 'form-control select2 select2-hidden-accessible',
);
$select_data = array(
    'nickname' => '昵称',
    'account' => '帐号'
);
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <form action="" method="post" class="form form-horizontal">
            <div class="row">
                <div class="col-sm-1" style="float:left;padding-right:0;">
                    <?php echo $this->select($select_data, $this->e($data, 't'), $select_params); ?>
                </div>
                <div class="col-sm-2" style="float:left;padding-left:0;">
                    <div class="input-group">
                        <input type="text" class="form-control" name="key" value="<?php echo $this->e($data, 'key') ?>">
                        <span class="input-group-btn">
                          <button class="btn btn-primary btn-flat" type="submit">搜索</button>
                        </span>
                    </div>
                </div>
                <div class="col-sm-1">
                    <?php if (isset($data['t'])): ?>
                        <div style="line-height: 30px;">
                            <a href="<?php echo $this->url('user') ?>">清除搜索结果</a>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </form>
    </div>
    <div class="box-body table-responsive">
        <?php if (!empty($data['user_list'])) : ?>
            <table class="table table-bordered border-hover">
                <tr>
                    <th>UID</th>
                    <th style="min-width:90px;">帐号</th>
                    <th style="min-width:100px;">昵称</th>
                    <th style="min-width:100px;">简介</th>
                    <th style="min-width:120px;">二维码</th>
                    <th style="min-width:120px;">头像</th>
                    <th style="min-width:100px;">最近登录IP</th>
                    <th style="min-width:160px;">最近登录时间</th>
                    <th style="min-width:100px;">注册IP</th>
                    <th style="min-width:160px;">注册时间</th>
                    <th style="min-width:150px;">操作</th>
                </tr>
                <?php foreach ($data['user_list'] as $u) : ?>
                    <tr>
                        <td><?php echo $u['uid'] ?></td>
                        <td><?php echo $u['account'] ?></td>
                        <td><?php echo $u['nickname'] ?></td>
                        <td><?php echo $u['introduce'] ?></td>
                        <td>
                            <?php if (empty($u['qr'])) : ?>
                                -
                            <?php else : ?>
                                <a href="<?php echo $this->getResource($u['qr']) ?>" data-image-url="<?php echo $this->getResource($u['qr']) ?>"
                                   class="preview" data-container="body" data-placement="top">二维码</a>
                            <?php endif ?>
                        </td>
                        <td>
                            <a href="<?php echo $this->getResource($u['avatar']) ?>" data-image-url="<?php echo $this->getResource($u['avatar']) ?>"
                               class="preview" data-container="body" data-placement="top">头像</a>
                        </td>
                        <td>
                            <?php echo long2ip($u['last_login_ip']) ?>
                        </td>
                        <td><?php echo date('Y-m-d H:i:s', $u['last_login_time']) ?></td>
                        <td>
                            <?php echo long2ip($u['register_ip']) ?>
                        </td>
                        <td><?php echo date('Y-m-d H:i:s', $u['register_time']) ?></td>
                        <td>
                            <?php
                            if ($u['status'] == -1) {
                                echo $this->a('解封', $this->url('user:ban', array('uid' => $u['uid'], 'act' => 'unban')), array(
                                    'style' => 'color:#ff0000'
                                ));
                            } else {
                                echo $this->a('封号', $this->url('user:ban', array('uid' => $u['uid'], 'act' => 'ban')));
                            }
                            ?>
                            <a href="javascript:void(0)" class="resetPassword" uid="<?php echo $u['uid'] ?>">重置密码</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else : ?>
            <div>暂无用户</div>
        <?php endif ?>
    </div>
    <div class="box-footer">
        <?php $this->page($data['page']) ?>
    </div>
</div>
<?php if (!empty($data['user_list'])) : ?>
    <script>
        $(function () {
            $('.preview').popover({
                'trigger': 'hover',
                'html': true,
                'content': function () {
                    var imageUrl = $(this).data('imageUrl');
                    if (imageUrl) {
                        return "<img src='" + $(this).data('imageUrl') + "' style='width:110px;'>";
                    } else {
                        return '暂无图片';
                    }
                }
            });

            $('.resetPassword').on('click', function () {
                var self = $(this);
                layer.prompt({
                    title: '请输入重置后的密码',
                    formType: 0
                }, function (pass) {
                    $.post('<?php echo $this->url('user:resetPassword') ?>', {
                        'uid': self.attr('uid'),
                        'password': pass
                    }, function (d) {
                        layer.msg(d.message);
                    });
                });
            })
        })
    </script>
<?php endif ?>
