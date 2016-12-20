<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * posts.php
 */
//print_r($posts_info);
$posts_info = &$data['posts_info'];
$editor_data = array('title_id' => $posts_info['title_id'], 'isLogin' => $data['isLogin'], 'loginUser' => $data['loginUser']);
$user_home_url = $this->url('user:detail', array('account' => $posts_info['account']));
$user_home_link = $this->a($posts_info['nickname'], $user_home_url, array('class' => 'ia'));
$posts_page = array();
if ($posts_info['content_page'] > 1) {
    $posts_page = array(
        'p' => $posts_info['p'],
        'half' => 5,
        'link' => array('content:posts', array('posts_id' => $posts_info['posts_id'], 'order' => $data['order'])),
        'total_page' => $posts_info['content_page']
    );
}

?>
<div class="container posts-detail">
    <div class="row">

        <div class="col-md-9">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-cpf-content content-box">
                        <div class="panel-heading">
                            <div class="content-topic-list">
                                <?php echo $this->contentTopics($posts_info['topics'], 'posts') ?>
                            </div>

                            <div class="media">
                                <div class="media-body">
                                    <div class="content-title"><?php echo $posts_info['title'] ?></div>
                                    <div class="user-info">
                                        <?php printf('%s 于 %s', $user_home_link, $this->ftime($posts_info['post_time'])) ?>
                                    </div>
                                </div>
                                <div class="media-right">
                                    <a href="<?php echo $this->url('user:detail', array('account' => $posts_info['account'])) ?>">
                                        <?php echo $this->userAvatar($posts_info['avatar']) ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div id="posts_content" class="content">
                                <?php echo $posts_info['content'] ?>
                            </div>
                            <div class="tac">
                                <?php
                                if ($posts_page) {
                                    $this->page($posts_page, 'title');
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top:20px;">
                <div class="col-md-12">
                    <div class="panel panel-cpf-content">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-5 ft18">
                                    <?php echo $data['page']['result_count'] ?> 条回复
                                </div>
                                <div class="col-xs-7 tar order-menu">
                                    <?php $this->replyOrderMenu($posts_info['posts_id'], $data['order']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <?php
                            if (!empty($data['reply_list'])) {
                                foreach ($data['reply_list'] as $reply) {
                                    $this->renderTpl('content/segment/reply', $reply);
                                }
                            }

                            //最后一页显示被屏蔽的答案数
                            if ($data['page']['p'] >= $data['page']['total_page'] && $data['blocked_reply_count'] > 0) {
                                echo $this->wrap('div', array('class' => 'blocked-content-list'))
                                    ->a("有{$data['blocked_reply_count']}个回复被折叠或屏蔽, 点击查看", 'javascript:void(0)', array(
                                        'id' => 'loadBlockReply', 'posts-id' => $posts_info['posts_id']
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
            if ($data['page']['result_count'] < 3) {
                $this->renderTpl('fragment/invite/base', array(
                    'title' => '邀请讨论',
                    'topics' => $posts_info['topic_ids'],
                    'title_id' => $posts_info['title_id'],
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
                    <?php $this->renderTpl('fragment/slide/content_action', $posts_info) ?>
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
        $('.reply-list-flag').hover(function () {
            $(this).find('.reply-control-panel').show();
        }, function () {
            $(this).find('.reply-control-panel').hide();
        });

        $('.report-flag').on('click', function () {
            var report_id = $(this).attr('report-id'), report_type = $(this).attr('report-type');
            $.post('<?php echo $this->url('action:report') ?>', {'type': report_type, 'id': report_id});
            layer.msg('我们已收到您的举报');
        });

        //内容检测
        $('#contentForm').on('submit', function () {
            if (!$('#editor').val()) {
                layer.msg('回复内容不能为空');
                return false;
            }
        });

        //显示被屏蔽或折叠的答案
        $('#loadBlockReply').on('click', function () {
            var posts_id = $(this).attr('posts-id'), loaded = $(this).attr('loaded'), that = $(this);
            if (loaded) {
                $('#blockContentArea').toggle()
            } else {
                $.post('<?php echo $this->url('action:loadBlockReply') ?>', {'posts_id': posts_id}, function (d) {
                    that.attr('loaded', 1);
                    $('#blockContentArea').html(d);
                });
            }
        });

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
    })
</script>

