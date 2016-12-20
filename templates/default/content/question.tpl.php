<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * question.tpl.php
 */
//print_r($data);

$question = &$data['question_info'];
$user_home_url = $this->url('user:detail', array('account' => $question['account']));
$user_home_link = $this->a($question['nickname'], $user_home_url, array('class' => 'ia'));
$editor_data = array('title_id' => $question['title_id'], 'isLogin' => $data['isLogin'], 'loginUser' => $data['loginUser']);
?>
<div class="container question-detail">
    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-cpf-content content-box">
                        <div class="panel-heading">

                            <div class="content-topic-list">
                                <?php echo $this->contentTopics($question['topics'], 'question') ?>
                            </div>

                            <div class="media">
                                <div class="media-body">
                                    <h4 class="content-title"><?php echo $question['title'] ?></h4>
                                    <div class="user-info">
                                        <?php printf('%s 于 %s', $user_home_link, $this->ftime($question['post_time'])) ?>
                                    </div>
                                </div>
                                <div class="media-right">
                                    <a href="<?php echo $user_home_url ?>">
                                        <?php echo $this->userAvatar($question['avatar']) ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body content">
                            <?php echo $question['question_content'] ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel-cpf-answer">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-6 ft18">
                                    <?php echo $data['page']['result_count'] ?> 个回答
                                </div>
                                <div class="col-xs-6 tar order-menu">
                                    <?php $this->answerOrderMenu($question['question_id'], $data['order']) ?>
                                </div>
                            </div>
                        </div>

                        <div class="panel-body">
                            <?php
                            if (!empty($data['answer_list'])) {
                                foreach ($data['answer_list'] as $answer) {
                                    $this->renderTpl('content/segment/answer', $answer);
                                }
                            }

                            //最后一页显示被屏蔽的答案数
                            if ($data['page']['p'] >= $data['page']['total_page'] && $data['blocked_answer_count'] > 0) {
                                echo $this->wrap('div', array('class' => 'blocked-content-list'))
                                    ->a("有{$data['blocked_answer_count']}个答案被折叠或屏蔽, 点击查看", 'javascript:void(0)', array(
                                        'id' => 'loadBlockAnswer', 'question-id' => $question['question_id']
                                    ));
                            }
                            ?>
                            <div id="blockContentArea"></div>
                        </div>

                        <div class="panel-footer">
                            <?php echo $this->page($data['page']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            //邀请回答
            if ($data['page']['result_count'] < 3) {
                $this->renderTpl('fragment/invite/base', array(
                    'title' => '邀请回答',
                    'topics' => $question['topic_ids'],
                    'title_id' => $question['title_id'],
                ));
            }
            ?>
            <div>
                <?php $this->renderTpl('fragment/editor/content_form', $editor_data) ?>
            </div>
        </div>

        <div class="col-md-3">
            <div class="row col-right">
                <div class="col-md-12">
                    <?php $this->renderTpl('fragment/slide/content_action', $question) ?>
                </div>
            </div>
            <?php
            if (!empty($data['correlation_content'])) {
                $this->renderTpl('fragment/slide/correlation_content', $data['correlation_content']);
            }
            ?>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('.answer-list-flag').hover(function () {
            $(this).find('.answer-control-panel').show();
        }, function () {
            $(this).find('.answer-control-panel').hide();
        });

        //举报
        $('.report-flag').on('click', function () {
            var report_id = $(this).attr('report-id'), report_type = $(this).attr('report-type');
            $.post('<?php echo $this->url('action:report') ?>', {'type': report_type, 'id': report_id});
            layer.msg('我们已收到您的举报');
        });

        //内容检测
        $('#contentForm').on('submit', function () {
            if (!$('#editor').val()) {
                layer.msg('您的答案不能为空');
                return false;
            }
        });

        //显示被屏蔽或折叠的答案
        $('#loadBlockAnswer').on('click', function () {
            var question_id = $(this).attr('question-id'), loaded = $(this).attr('loaded'), that = $(this);
            if (loaded) {
                $('#blockContentArea').toggle()
            } else {
                $.post('<?php echo $this->url('action:loadBlockAnswer') ?>', {'question_id': question_id}, function (d) {
                    that.attr('loaded', 1);
                    $('#blockContentArea').html(d);
                });
            }
        });

        //投票
        $('.act-flag').on('click', function () {
            var self = $(this);
            var act = $(this).attr('act'),
                question_id = $(this).attr('question-id'),
                answer_id = $(this).attr('answer-id');

            if (!act || !question_id || !answer_id) {
                layer.msg("操作失败");
                return;
            }

            $.post('<?php echo $this->url('action:vote') ?>', {
                'act': act,
                'question_id': question_id,
                'answer_id': answer_id
            }, function (d) {
                if (typeof d != "object") {
                    d = $.parseJSON(d);
                }

                if (d.status != 1) {
                    layer.msg(d.message);
                } else {
                    if (d.data.stand == 0) {
                        self.removeClass('act').siblings().removeClass('act');
                    } else {
                        self.addClass('act').siblings().removeClass('act');
                    }
                    document.getElementById('up-count-' + answer_id).innerHTML = d.data.up_count;
                }
            });
        });
    });
</script>
