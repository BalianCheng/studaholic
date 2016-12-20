<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * recommend_list.tpl.php
 */
$list = &$data['list'];
?>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs pull-right">
        <li class="pull-left header">
            <i class="fa fa-th"></i>内容列表
        </li>
    </ul>
    <div class="tab-content">
        <form action="" method="post">
            <div class="table-responsive">
                <div class="box-body">
                    <?php if (!empty($list)) : ?>
                        <table class="table table-bordered table-hover">
                            <tr>
                                <th style="width:40px;max-width:40px;">ID</th>
                                <th style="width:360px;min-width:360px;">标题</th>
                                <th style="width:100px;max-width:100px;">发布IP</th>
                                <th style="width:180px;min-width:180px;">发布时间</th>
                                <th style="width:180px;min-width:180px;">推荐时间</th>
                                <th style="width:60px;min-width:60px;">推荐排序</th>
                                <th style="width:120px;min-width:120px;">操作</th>
                            </tr>
                            <?php foreach ($list as $l) : ?>
                                <tr>
                                    <td><?php echo $l['recommend_id'] ?></td>
                                    <td>
                                        <a href="javascript:void(0)" class="view-content-flag"
                                           content-id="<?php echo $l['title_id'] ?>">
                                            <?php echo $l['title'] ?>
                                        </a>
                                        <a href="<?php echo $this->url('forum:jumpToContent', array('title_id' => $l['title_id'])) ?>"
                                           target="_blank">
                                            <i class="fa fa-external-link" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo long2ip($l['post_ip']) ?>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i:s', $l['post_time']) ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', $l['recommend_time']) ?></td>
                                    <td>
                                        <input type="text" class="form-control"
                                               name="<?php echo $l['recommend_id'] ?>[sort]"
                                               value="<?php echo $l['recommend_sort'] ?>"/>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" class="recommend-flag cancel-recommend"
                                           recommend-id="<?php echo $l['recommend_id'] ?>"
                                           title-id="<?php echo $l['title_id'] ?>">取消推荐</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else : ?>
                        <div>暂无推荐内容</div>
                    <?php endif ?>
                </div>
                <div class="box-footer">
                    <input type="submit" class="btn btn-primary" value="保存">
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="authorEditModal"></div>
<div class="modal fade" id="content-preview"></div>
<script src="<?php echo $this->res('js/jquery.serializejson.min.js') ?>"></script>
<script>
    $(function () {

        $('.view-content-flag').on('click', function () {
            var content_id = $(this).attr('content-id');
            $.post('<?php echo $this->url('content:preview') ?>', {
                "title_id": content_id,
                "max_width": $(window).width()
            }, function (d) {
                $('#content-preview').html(d).modal();
            });
        });

        $('.recommend-flag').on('click', function () {
            var title_id = $(this).attr('title-id'), recommend_id = $(this).attr('recommend-id'), that = $(this);
            $.post("<?php echo $this->url('content:recommend') ?>", {
                'recommend_id': recommend_id,
                'title_id': title_id
            }, function (d) {
                if (d.type == 'error') {
                    layer.msg('参数不正确');
                } else {
                    if (d.ret) {
                        that.attr('recommend-id', d.recommend_id);
                        if (d.type == 'recommend') {
                            that.addClass('cancel-recommend').html('取消推荐');
                        } else {
                            that.removeClass('cancel-recommend').html('编辑推荐');
                        }
                    } else {
                        layer.msg('操作失败');
                    }
                }
            })
        })
    })
</script>
