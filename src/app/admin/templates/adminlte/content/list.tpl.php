<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * list.tpl.php
 */
$list = &$data['list'];
$page = $data['page'];
$select_data = &$data['searchOption'];
$select_params = array(
    'name' => 'filter_id',
    'class' => 'form-control select2 select2-hidden-accessible',
);
?>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs pull-right">
        <?php
        if ($this->params['filter_type'] == 'status') {
            $attr = array('class' => 'active');
            $normal_attr = array();
        } else {
            $attr = array();
            $normal_attr = array('class' => 'active');
        }

        echo $this->wrap('li', $attr)
            ->a('已屏蔽', $this->url('content:index', array('filter_type' => 'status', 'filter_id' => 0)));

        echo $this->wrap('li', $normal_attr)
            ->a('正常', $this->url('content:index'));
        ?>

        <li class="pull-left header">
            <i class="fa fa-th"></i>
            <?php
            if (!empty($data['filterTips'])) {
                echo $data['filterTips'] . '(' . $page['result_count'] . ')' . $this->a(' 重置 ', $this->url('content:index'), array(
                        'style' => 'display:inline-block',
                    ));
            } else {
                echo '内容列表';
            }
            ?>
        </li>
    </ul>
    <div class="tab-content">
        <div class="table-responsive">
            <div class="box-body">
                <?php if (!empty($list)) : ?>
                    <div class="" style="margin-bottom:10px;margin-left:-15px;display:table">
                        <form action="" id="search-form" method="post" class="form form-horizontal">
                            <div class="col-sm-4 col-md-4 col-xs-4 col-lg-4" style="float:left;padding-right:0;">
                                <?php echo $this->select($select_data, $this->e($data, 't'), $select_params); ?>
                            </div>
                            <div class="col-sm-8 col-md-8 col-xs-8 col-lg-8" style="float:left;padding-left:0;">
                                <div class="input-group">
                                    <input type="hidden" name="filter_type" value="search">
                                    <input type="text" class="form-control" name="key"
                                           value="<?php echo $this->e($data, 'key') ?>">
                        <span class="input-group-btn">
                          <button class="btn btn-primary btn-flat" type="submit">搜索</button>
                        </span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <table class="table table-bordered table-hover">
                        <tr>
                            <th style="width:40px;max-width:40px;">ID</th>
                            <th style="width:360px;min-width:360px;">标题</th>
                            <th style="width:120px;min-width:120px;">所属话题</th>
                            <th style="width:60px;min-width:60px;">类型</th>
                            <th style="width:80px;min-width:80px;">作者</th>
                            <th style="width:100px;max-width:100px;">发布IP</th>
                            <th style="width:180px;min-width:180px;">发布时间</th>
                            <th style="width:138px;min-width:138px;">操作</th>
                        </tr>
                        <?php foreach ($list as $l) : ?>
                            <tr>
                                <td><?php echo $l['title_id'] ?></td>
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
                                    <?php echo $l['topic_names'] ?>
                                </td>
                                <td>
                                    <a href="<?php echo $l['type_filter_link'] ?>">
                                        <?php echo $l['type_name'] ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?php echo $l['user_filter_link'] ?>">
                                        <?php echo $l['author_name'] ?>
                                    </a>
                                </td>
                                <td><?php echo $l['post_ip'] ?></td>
                                <td><?php echo $l['post_time'] ?></td>
                                <td>
                                    <?php
                                    $attr = array(
                                        'title-id' => $l['title_id'],
                                        'status' => $l['status']
                                    );

                                    if ($l['status'] == 0) {
                                        $txt = '取消屏蔽';
                                        $attr['class'] = 'block-flag blocked-content';
                                    } else {
                                        $txt = '屏蔽内容';
                                        $attr['class'] = 'block-flag';
                                    }

                                    echo $this->a($txt . '&nbsp;', 'javascript:void(0)', $attr);

                                    $attr = array(
                                        'title-id' => $l['title_id'],
                                        'recommend-id' => $l['recommend_id'],
                                    );
                                    if ($l['recommend_id'] > 0) {
                                        $txt = '取消推荐';
                                        $attr['class'] = 'recommend-flag cancel-recommend';
                                    } else {
                                        $txt = '编辑推荐';
                                        $attr['class'] = 'recommend-flag';
                                    }

                                    echo $this->a($txt, 'javascript:void(0)', $attr);
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </table>
                <?php else : ?>
                    <div>暂无内容</div>
                <?php endif ?>
            </div>
            <div class="box-footer">
                <?php $this->page($data['page']) ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="authorEditModal"></div>
<div class="modal fade" id="content-preview"></div>
<script src="<?php echo $this->res('js/jquery.serializejson.min.js') ?>"></script>
<script>
    $(function () {
        $('#search-form').on('submit', function () {
            var data = $(this).serializeJSON(), url = '<?php echo $this->url('content:index', array(
                'filter_type' => '::FILTER_TYPE::',
                'filter_id' => '::FILTER_ID::',
                'key' => '::KEY::'
            )) ?>';

            url = url.replace('::FILTER_TYPE::', data.filter_type)
                .replace('::FILTER_ID::', data.filter_id)
                .replace('::KEY::', data.key);

            location.href = url;
            return false;
        });

        $('.view-content-flag').on('click', function () {
            var content_id = $(this).attr('content-id');
            $.post('<?php echo $this->url('content:preview') ?>', {
                "title_id": content_id,
                "max_width": $(window).width()
            }, function (d) {
                $('#content-preview').html(d).modal();
            });
        });

        $('.block-flag').on('click', function () {
            var that = $(this);
            $.post('<?php echo $this->url('content:blockContentUI') ?>', {'title_id': that.attr('title-id')}, function (d) {
                var modalEle = $('#authorEditModal');
                modalEle.html(d).modal();
                $('#blockButton').on('click', function () {
                    $.post('<?php echo $this->url('content:blockContent') ?>',
                        {'title_id': that.attr('title-id'), 'reason': $('#blockTxt').val()},
                        function (d) {
                            if (d.type == 'block') {
                                that.attr('status', 0).addClass('blocked-content').html('取消屏蔽');
                            } else {
                                that.attr('status', 1).removeClass('blocked-content').html('屏蔽内容');
                            }
                            modalEle.html();
                            modalEle.modal('hide');
                        })
                })
            })
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
