<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * answer.tpl.php
 */
$page = $data['page'];
$question = &$data['question_info'];
$answer = &$data['answer_info'];
$editor_data = array('isLogin' => $data['isLogin'], 'loginUser' => $data['loginUser']);

$page_params = $page['link'][1];
$page_params['p'] = ":page:";
$pagingUrl = $this->url($page['link'][0], $page_params);

$content_url = $this->url('content:question', array('question_id' => $question['question_id']));


$up_class = $down_class = '';
if ($data['stand'] == 1) {
    $up_class = 'act';
}

if ($data['stand'] == 2) {
    $down_class = 'act';
}

$pageLessConfig = array(
    'totalPages' => $page['total_page'],
    'currentPage' => $page['p'],
    'url' => $pagingUrl,
    'loaderImage' => $this->res('images/load_content.gif'),
    'loaderMsg' => '内容加载中',
    'endMsg' => '没有更多了',
);
?>
<div class="container" style="margin-top: 60px;">
    <div class="row">
        <div class="col-md-9 col-centered">
            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->contentTopics($question['topics'], 'question') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h3>
                        <a href="<?php echo $content_url ?>" class="ia">
                            <?php echo $question['title'] ?>
                        </a>
                    </h3>
                    <div class="content" style="margin:10px 0">
                        <?php echo $question['question_content'] ?>
                    </div>
                    <div class="border-bottom"></div>
                </div>
                <div class="col-md-12 ft14">
                    <a href="<?php echo $content_url ?>" style="margin:15px 0;display: block">
                        查看全部回答
                    </a>
                    <div class="border-bottom"></div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-12" style="margin:5px 0">
                    <div class="row">
                        <div class="col-xs-9 user-info">
                            <?php echo $this->userNickname($answer['account'], $answer['nickname'], $answer['introduce'], true, array('class' => 'ia')); ?>
                        </div>
                        <div class="col-xs-3 tar">
                            <a href="<?php echo $this->url('user:detail', array('account' => $answer['account'])) ?>">
                                <?php echo $this->userAvatar($answer['avatar'], '24px') ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="media">
                        <div class="media-left answer-act">
                        <span class="btn btn-up act-flag <?php echo $up_class ?>" act="up"
                              question-id="<?php echo $question['question_id'] ?>"
                              answer-id="<?php echo $answer['answer_id'] ?>">
                            <i class="iconfont-small icon-up"></i>
                            <span class="up-count-flag" id="up-count-<?php echo $answer['answer_id'] ?>">
                                <?php echo $answer['up_count'] ?>
                            </span>
                        </span>
                        <span class="btn btn-down act-flag <?php echo $down_class ?>" act="down"
                              question-id="<?php echo $question['question_id'] ?>"
                              answer-id="<?php echo $answer['answer_id'] ?>">
                            <i class="iconfont-small icon-down"></i>
                        </span>
                        </div>

                        <div class="media-body">
                            <div class="row">
                                <div class="col-md-12 content">
                                    <?php echo $answer['answer_content'] ?>
                                </div>
                                <div class="col-md-12">
                                    <small>发布于 <?php echo $this->fTime($answer['answer_time']) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row" style="margin-top:10px;">
                <div class="col-md-12">
                    <div class="section-title">
                        添加新评论
                    </div>
                    <?php $this->renderTpl('fragment/editor/simple_form', $data) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="section-title">
                        <?php echo $page['result_count'] ?> 条评论
                    </div>
                </div>
            </div>

            <div class="row" id="comment_list">
                <?php
                if (!empty($data['comment_list'])) {
                    $this->commentList($data['comment_list']);
                } else {
                    echo '<div class="col-md-12 empty-tip">暂无评论</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $this->res('libs/jquery_pageless/jquery.pageless.js') ?>"></script>
<script>
    $(function () {
        $('#comment_list').pageless(<?php echo json_encode($pageLessConfig) ?>);

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
