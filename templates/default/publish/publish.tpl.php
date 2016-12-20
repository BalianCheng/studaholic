<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * question.tpl.php
 */
$addition_data = $data['addition']['data'];
$addition_data['isLogin'] = $data['isLogin'];
$addition_data['loginUser'] = $data['loginUser'];

//不同类型的私有id和值
$content_id = 0;
$addition_topic_ids = array();
$content_type = &$data['save_type'];
$content_name = $content_type . '_id';
if (isset($addition_data[$content_name])) {
    $content_id = $addition_data[$content_name];
}

//修改时的话题数据
if (!empty($addition_data['topics'])) {
    foreach ($addition_data['topics'] as $t) {
        $addition_topic_ids[] = $t['topic_id'];
    }
}
?>
<div class="container publish">
    <div class="row">
        <div class="col-md-12">
            <form id="publishForm" method="post"
                  action="<?php echo $this->url('publish:save', array('type' => $data['save_type'], 'csrf_token' => $this->e($data, 'csrf_token'))) ?>">
                <div class="form-group">
                    <label for="title"><?php echo $this->e($data, 'title') ?></label>
                    <input type="hidden" name="p" value="<?php echo $this->e($addition_data, 'p') ?>">
                    <input type="hidden" name="title_id" value="<?php echo $this->e($addition_data, 'title_id') ?>">
                    <input type="hidden" name="<?php echo $content_name ?>" value="<?php echo $content_id ?>">
                    <input type="text" class="form-control input-lg" id="title" name="title"
                           placeholder="<?php echo $this->e($data, 'title_placeholder') ?>"
                           value="<?php echo $this->e($addition_data, 'title') ?>">
                </div>

                <div class="form-group">
                    <label for="topic">添加话题</label>
                    <select id="topic" name="topic_ids[]" class="form-control" multiple="multiple"></select>
                    <div style="padding-top:5px;">
                        <ul class="nav nav-pills nav-topic choose-topic-type">
                            <li role="presentation" topic="following">
                                <a href="javascript:void(0)">我关注的话题</a>
                            </li>
                            <li role="presentation" topic="recommend">
                                <a href="javascript:void(0)">推荐话题</a>
                            </li>
                        </ul>
                        <div id="topic-choose-menu" style="padding-top:5px;"></div>
                    </div>
                </div>
                <?php
                if (!empty($data['addition'])) {
                    $this->renderTpl($data['addition']['template'], $addition_data);
                }
                ?>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo $this->res('css/select2-bootstrap.min.css') ?>">
<script src="<?php echo $this->res('libs/select2/4.0.2/js/select2.full.min.js') ?>"></script>
<script src="<?php echo $this->res('js/select2-language.js') ?>"></script>
<script>
    $(function () {

        var hashTopics = window.location.hash.substring(1), topic_dom_ids = [];
        var select = $("#topic").select2({
            multiple: true,
            language: "zh-CN",
            theme: 'bootstrap',
            minimumInputLength: 1,
            maximumSelectionLength: 5,
            ajax: {
                url: "<?php echo $this->url('action:findTopic') ?>",
                dataType: 'json',
                method: 'post',
                delay: 250,
                data: function (params) {
                    window.topic = {term: params.term};
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data.items,
                        pagination: {
                            more: (params.page * 30) < data.data.page.result_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            templateSelection: function (repo) {
                var topic_id = Number(repo.id);
                if ($.inArray(topic_id, topic_dom_ids) < 0) {
                    topic_dom_ids.push(topic_id);
                }

                return repo.text;
            }
        });

        if (hashTopics) {
            $.post('<?php echo $this->url('action:findTopicByName') ?>', {
                'topics': hashTopics,
                'type': '<?php echo $content_type ?>'
            }, function (d) {
                if (d.data.length > 0) {
                    makeTopicOptions(d.data, 1);
                    var topic_ids = [];
                    for (var i = 0, j = d.data.length; i < j; i++) {
                        if (d.data[i]['can_choose']) {
                            topic_ids.push(d.data[i]['topic_id']);
                        }
                    }
                    select.val(topic_ids).trigger('change');
                }
            });
        }

        <?php if (!empty($addition_data['topics'])) : ?>
        makeTopicOptions(<?php echo json_encode($addition_data['topics']) ?>);
        select.val(<?php echo json_encode($addition_topic_ids) ?>).trigger('change');
        <?php endif ?>

        $('.choose-topic-type>li').on('click', function () {
            $(this).addClass('active').siblings().removeClass('active');
            var topic = $(this).attr('topic');
            if (topic == 'following') {
                $.post('<?php echo $this->url('action:getUserFollowingTopics') ?>', {'type': '<?php echo $content_type ?>'}, function (d) {
                    makeTopicOptions(d.data, 1);
                })
            } else {
                $.post('<?php echo $this->url('action:getRecommendTopics') ?>', {'type': '<?php echo $content_type ?>'}, function (d) {
                    makeTopicOptions(d.data, 1);
                })
            }
        });

        $('body').on('click', '.topic-choose-flag', function () {
            var topic_id = $(this).attr('topic-id'), choose_topics = select.val() || [];
            if (choose_topics.length < 5) {
                choose_topics.push(topic_id);
            } else {
                layer.msg('最多只能添加五个话题');
            }

            select.val(choose_topics).trigger('change')
        }).on('click', '.invalid-topic', function () {
            layer.msg('不能添加该话题');
        });

        $('#publishForm').on('submit', function () {
            var title = $('#title').val(), contentType = '<?php echo $content_type ?>', contentText = editor.$txt.text();
            if (!title) {
                layer.msg("请输入标题");
                return false;
            }

            if (contentType != 'question' && !contentText) {
                layer.msg("内容不能为空");
                return false;
            }

            return true;
        });

        function makeTopicOptions(data, display_menu) {
            var append_dom = '', choose_menu = '', dataLength = data.length;
            if (dataLength > 0) {
                for (var i = 0; i < dataLength; i++) {
                    var canChoose = data[i]['can_choose'], cc = 'topic-choose-flag';
                    if ($.inArray(data[i]['topic_id'], topic_dom_ids) < 0) {
                        if(canChoose) {
                            topic_dom_ids.push(data[i]['topic_id']);
                            append_dom += '<option value="' + data[i]['topic_id'] + '">' + data[i]['topic_name'] + '</option>';
                        }
                    }

                    if (!canChoose) {
                        cc = 'invalid-topic'
                    }

                    choose_menu += '<a href="javascript:void(0)" class="' + cc + '" topic-id="' + data[i]['topic_id'] + '">' + data[i]['topic_name'] + '</a>'
                }
            } else {
                choose_menu = '暂无';
            }

            $('#topic').append(append_dom);
            if (display_menu == 1) {
                $('#topic-choose-menu').html(choose_menu);
            }
        }

    });
</script>
