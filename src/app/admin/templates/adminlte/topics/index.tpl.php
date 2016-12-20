<?php
$parent_id = &$data['parent_id'];
?>
<div class="row">
    <div class="col-md-2">
        <div class="box box-primary">
            <div class="box-body">
                <ul class="list-unstyled">
                    <?php
                    if (!empty($data['rootTopics'])) {
                        foreach ($data['rootTopics'] as $rTopic) {
                            $this->renderTpl('topics/fragment/root_topic', $rTopic);
                        }
                    }
                    ?>
                </ul>
            </div>
            <div class="box-footer">
                <button class="btn btn-default" id="newRootTopic" topic-id="0"
                        style="display:block;text-align:center;min-width:100%">添加分类
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-10">
        <form action="" class="form" method="post">
            <div class="box">
                <div class="box-header">
                    <a href="javascript:void(0)" id="newTopic" parent-id="<?php echo $parent_id ?>"
                       class="btn btn-success">添加话题</a>
                    <a href="javascript:void(0)" id="editRootTopic" topic-id="<?php echo $parent_id ?>"
                       class="btn btn-success">编辑分类信息</a>
                    <a href="javascript:void(0)" topic-id="<?php echo $parent_id ?>"
                       class="del-topic-flag btn btn-danger">删除此分类</a>
                </div>
                <?php if (!empty($data['currentTopics'])) : ?>
                    <div class="box-body table-responsive">
                        <table class="table">
                            <tr>
                                <th style="width:40px;min-width:40px;">ID</th>
                                <th style="width:120px;min-width:120px;">名称</th>
                                <th style="width:150px;min-width:120px;">访问名称</th>
                                <th style="width:80px;min-width:80px;">是否推荐</th>
                                <th style="width:80px;min-width:80px;">问题</th>
                                <th style="width:80px;min-width:80px;">帖子</th>
                                <th style="width:80px;min-width:80px;">文章</th>
                                <th style="min-width:80px;width:80px;">排序</th>
                                <th style="min-width:150px;width:150px;">操作</th>
                            </tr>
                            <?php
                            foreach ($data['currentTopics'] as $cTopic) {
                                $this->renderTpl('topics/fragment/current_topic', $cTopic);
                            }
                            ?>
                        </table>
                    </div>
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="保存">
                    </div>
                <?php endif ?>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="topicManager"></div>
<div class="modal fade" id="topicSaveModal"></div>
<div class="modal fade" id="newRootTopicModal"></div>
<script src="<?php echo $this->res('js/jquery.uploadPreview.min.js') ?>"></script>
<script>

    /**
     * 检测话题url
     *
     * @param topic_url
     * @param topic_id
     * @returns {int}
     */
    function checkTopicUrl(topic_url, topic_id) {
        var isHave = 0;
        $.ajax({
            type: 'POST',
            url: '<?php echo $this->url('topics:checkTopicUrl') ?>',
            data: {'topic_url': topic_url, 'topic_id': topic_id || ''},
            async: false,
            complete: function (d) {
                isHave = d.responseJSON.isHave;
            }
        });

        return isHave;
    }

    $(function () {
        $('#newTopic, .edit-topic-flag').on('click', function () {
            var parent_id = $(this).attr('parent-id'), topic_id = $(this).attr('topic-id');
            $.post("<?php echo $this->url('topics:saveTopicUI') ?>", {pid: parent_id, id: topic_id}, function (d) {
                var ele = $('#topicSaveModal');
                ele.html(d);
                $('.modal-toggle-flag').bootstrapToggle();
                ele.modal();

                $.uploadPreview({
                    input_field: "#image-upload",
                    preview_box: "#image-preview",
                    label_field: "#image-label",
                    label_default: "",
                    label_selected: "",
                    no_label: false
                });

                $('#topicSaveForm').on('submit', function () {
                    var topic_name = $('#topic_name').val(), topic_url = $('#topic_url').val();
                    if (!topic_name) {
                        layer.msg('话题名称不能为空');
                        return false;
                    }
                    if (!topic_url) {
                        layer.msg('话题访问地址不能为空');
                        return false;
                    } else {
                        if (checkTopicUrl(topic_url, topic_id)) {
                            layer.msg('话题访问地址已存在');
                            return false;
                        }
                    }
                })
            })
        });

        $('#editRootTopic, #newRootTopic').on('click', function () {
            var topic_id = $(this).attr('topic-id');
            $.post("<?php echo $this->url('topics:saveRootTopicUI') ?>", {'id': topic_id}, function (d) {
                var ele = $('#newRootTopicModal');
                ele.html(d);
                $('.modal-toggle-flag').bootstrapToggle();
                ele.modal();

                $('#rootTopicForm').on('submit', function () {
                    var root_topic_name = $('#root_topic_name').val(), root_topic_url = $('#root_topic_url').val();
                    if (!root_topic_name) {
                        layer.msg('话题名称不能为空');
                        return false;
                    }
                    if (!root_topic_url) {
                        layer.msg('话题访问地址不能为空');
                        return false;
                    } else {
                        if (checkTopicUrl(root_topic_url, topic_id)) {
                            layer.msg('话题访问地址已存在');
                            return false;
                        }
                    }
                })
            })
        });

        $('.topic-manager-flag').on('click', function () {
            var topic_id = $(this).attr('topic-id');
            $.post('<?php echo $this->url('topics:managerUI') ?>', {'topic_id': topic_id}, function (d) {
                var ele = $('#topicManager');
                ele.html(d).modal();

                $('#saveEditorButton').on('click', function () {
                    var editor_list = $('#editor_list').val();
                    if (!editor_list) {
                        layer.msg('请输入编辑UID');
                    } else {
                        $.post('<?php echo $this->url('topics:saveManager') ?>', {
                            'topic_id': topic_id,
                            'editor_list': editor_list
                        }, function (d) {
                            if (d.status == 1) {
                                layer.msg('设置成功');
                                ele.html('');
                                ele.modal('hide');
                            } else {
                                layer.msg('设置失败,请联系管理员');
                            }
                        })
                    }
                })
            });
        });

        $('.del-topic-flag').on('click', function () {
            var topic_id = $(this).attr('topic-id');
            layer.confirm("确定删除该话题吗?", {
                title: false,
                btn: ['确定', '取消']
            }, function () {
                $.post('<?php echo $this->url('topics:delTopic') ?>', {'topic_id': topic_id}, function (d) {

                    var delMsg = '';
                    if (d.delete) {
                        delMsg += '已成功删除以下话题:<br/>' + d.delete + '<br/>';
                    }

                    if (d.un_delete) {
                        delMsg += '以下子话题删除失败<br/>' + d.un_delete;
                    }

                    if (d.status == 1) {
                        var msg = '操作成功!';
                        if (delMsg) {
                            msg += "<br/>" + delMsg;
                        }

                        layer.msg(msg);
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    } else {
                        if (d.message) {
                            if (delMsg) {
                                d.message += "<br/>" + delMsg;
                            }

                            layer.msg(d.message);
                        } else {
                            layer.msg('删除话题失败');
                        }
                    }
                });

            });
        });
    })
</script>


