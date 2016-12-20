<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * answer.tpl.php
 */
$page = $data['page'];
$posts = &$data['posts_info'];
$reply = &$data['reply_info'];
$editor_data = array('isLogin' => $data['isLogin'], 'loginUser' => $data['loginUser']);

$page_params = $page['link'][1];
$page_params['p'] = ":page:";
$pagingUrl = $this->url($page['link'][0], $page_params);

$content_url = $this->url('content:posts', array('posts_id' => $posts['posts_id']));
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
                    <?php echo $this->contentTopics($posts['topics'], 'posts') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h3>
                        <a href="<?php echo $content_url ?>" class="ia">
                            <?php echo $posts['title'] ?>
                        </a>
                    </h3>
                    <div class="content">
                        <?php echo $posts['content'] ?>
                    </div>
                    <div class="border-bottom"></div>
                </div>
                <div class="col-md-12 ft14">
                    <a href="<?php echo $content_url ?>" style="margin:15px 0;display: block">
                        查看全部回复
                    </a>
                    <div class="border-bottom"></div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-12" style="margin:5px 0">
                    <div class="row">
                        <div class="col-xs-9 user-info">
                            <?php echo $this->userNickname($reply['account'], $reply['nickname'], $reply['introduce'], true, array('class' => 'ia')); ?>
                        </div>
                        <div class="col-xs-3 tar">
                            <a href="<?php echo $this->url('user:detail', array('account' => $reply['account'])) ?>">
                                <?php echo $this->userAvatar($reply['avatar'], '24px') ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 content">
                    <?php echo $reply['reply_content'] ?>
                </div>

                <div class="col-md-12">
                    <small>发布于 <?php echo $this->fTime($reply['reply_time']) ?></small>
                    <a href="javascript:void(0)" class="reply-up-flag ia" reply-id="<?php echo $reply['reply_id'] ?>">
                        <?php
                        if ($reply['is_up']) {
                            printf('<i class="iconfont-small act-icon icon-hand-down"></i>取消(%d)', $reply['up_count']);
                        } else {
                            printf('<i class="iconfont-small act-icon icon-hand-up"></i>支持(%d)', $reply['up_count']);
                        }
                        ?>
                    </a>
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
                if (!empty($data['reply_comment'])) {
                    $this->commentList($data['reply_comment']);
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
        $('.reply-up-flag').on('click', function () {
            var self = $(this);
            $.post('<?php echo $this->url('action:replyUp') ?>', {'reply_id': $(this).attr('reply-id')}, function (d) {
                if (d.status != 1) {
                    layer.msg(d.message);
                } else {
                    var data = d.data;
                    if (data.act_type == 0) {
                        self.html('<i class="iconfont-small act-icon icon-hand-up"></i>支持(' + data.up_count + ')');
                    } else {
                        self.html('<i class="iconfont-small act-icon icon-hand-down"></i>取消(' + data.up_count + ')');
                    }
                }
            })
        })
    });
</script>
