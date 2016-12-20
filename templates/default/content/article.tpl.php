<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * article.tpl.php
 */

$page = &$data['page'];
$article = &$data['article_info'];
$comment = &$data['comment_list'];
$user_home_url = $this->url('user:detail', array('account' => $article['account']));
$user_home_link = $this->a($article['nickname'], $user_home_url, array('class' => 'ia'));
$editor_data = array('title_id' => $article['title_id'], 'isLogin' => $data['isLogin'], 'loginUser' => $data['loginUser']);
$article_page = array();
if ($article['content_page'] > 1) {
    $article_page = array(
        'p' => $article['p'],
        'half' => 5,
        'link' => array('content:article', array('article_id' => $article['article_id'])),
        'total_page' => $article['content_page']
    );
}

//content_action仅输出button
$article['onlyButton'] = true;
?>
<div class="article-content">
    <div class="container-fluid article-heading">
        <div class="container">
            <div class="row">
                <div class="col-md-9 col-centered">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo $this->contentTopics($article['topics'], 'article') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 tac">
                            <h3 class="media-heading title"><?php echo $article['title'] ?></h3>
                        </div>
                        <div class="col-md-12 tac">
                            <?php $this->renderTpl('fragment/slide/content_action', $article) ?>
                        </div>
                    </div>

                    <div class="row" style="padding:20px 0;">
                        <div class="col-md-12 tac">
                            <div class="media">
                                <div class="">
                                    <a href="<?php echo $user_home_url ?>">
                                        <?php echo $this->userAvatar($article['avatar'], '68px') ?>
                                    </a>
                                </div>
                                <div class="media-body">
                                    <?php echo $user_home_link ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="container article-body">
        <div class="row">
            <div class="col-md-9 col-centered article-content content">
                <?php echo $article['content'] ?>
            </div>
        </div>
    </div>

    <div class="container article-footer">
        <?php if ($article['content_page'] > 1) : ?>
            <div class="row" style="margin: 20px 0">
                <div class="col-md-9 col-centered tac">
                    <?php $this->page($article_page, 'title') ?>
                </div>
            </div>
        <?php endif ?>

        <div class="row">
            <div class="col-md-9 col-centered" style="padding:20px 0;">
                <div class="row tac">
                    <div class="col-xs-6">
                        <a href="javascript:void(0)" class="nice-article-flag"
                           article-id="<?php echo $article['article_id'] ?>">
                            <img src="<?php echo $this->res('images/nice.png') ?>" alt="赞"/><br/>
                            <span>
                                <?php echo !empty($article['stand']) ? '已赞' : '赞' ?>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="padding-top:10px;">
            <div class="col-md-9 col-centered">
                <?php $this->renderTpl('fragment/editor/article_form', $editor_data) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9 col-centered">
                <div class="row">
                    <div class="col-xs-4 ft18" style="margin-top:10px;">
                        <?php echo $page['result_count'] ?> 条评论
                    </div>
                    <div class="col-xs-8 tar">
                        <?php echo $this->page($page, 'title') ?>
                    </div>
                </div>
                <hr/>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9 col-centered">
                <?php
                if(!empty($comment)) {
                    foreach ($comment as $d) {
                        $this->renderTpl('content/segment/comment', $d);
                    }
                }

                //最后一页显示被屏蔽的评论数
                if ($data['page']['p'] >= $data['page']['total_page'] && $data['blocked_comment_count'] > 0) {
                    echo $this->wrap('div', array('class' => 'blocked-content-list'))
                        ->a("有{$data['blocked_comment_count']}条评论被折叠或屏蔽, 点击查看", 'javascript:void(0)', array(
                            'id' => 'loadBlockComment', 'article-id' => $article['article_id']
                        ));
                }
                ?>
                <div id="blockContentArea"></div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid" style="margin-top:20px;">
    <div class="container">
        <div class="row">
            <div class="col-md-9 col-centered">
                <?php
                if (!empty($data['correlation_content'])) {
                    $this->renderTpl('fragment/article/recommend_content', $data['correlation_content']);
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $this->res("libs/artTemplate/3.0.1/template.js") ?>"></script>
<script id="affixTpl" type="text/html">
    {{if af.length > 0}}
    <div class="article-view hidden-print hidden-xs hidden-sm">
        <ul class="article-nav">
            {{each af as d}}
            <li>
                <a href="#{{d.id}}">{{d.name}}</a>
                {{if d.child}}
                <ul class="child-article-list">
                    {{each d.child as dd}}
                    <li><a href="#{{dd.id}}">{{dd.name}}</a></li>
                    {{/each}}
                </ul>
                {{/if}}
            </li>
            {{/each}}
        </ul>
    </div>
    {{/if}}
</script>
<script>
    $(function () {
        $('.comment-list-flag').hover(function () {
            $(this).find('.reply-control-panel').show();
        }, function () {
            $(this).find('.reply-control-panel').hide();
        });

        $('.report-flag').on('click', function () {
            var report_id = $(this).attr('report-id'), report_type = $(this).attr('report-type');
            $.post('<?php echo $this->url('action:report') ?>', {'type': report_type, 'id': report_id});
            layer.msg('我们已收到您的举报');
        });

        $('.nice-article-flag').on('click', function () {
            var n = $(this).find('span'),
                voteUrl = "<?php echo $this->url('action:articleVote') ?>",
                articleID = $(this).attr('article-id');

            $.post(voteUrl, {'article_id': articleID}, function (d) {
                if (d.status != 1) {
                    layer.msg(d.message);
                } else {
                    if (d.data.action == 1) {
                        n.html('已赞');
                    } else {
                        n.html('赞');
                    }
                }
            });
        });

        //显示被屏蔽或折叠的答案
        $('#loadBlockComment').on('click', function() {
            var article_id = $(this).attr('article-id'), loaded = $(this).attr('loaded'), that=$(this);
            if(loaded) {
                $('#blockContentArea').toggle()
            } else {
                $.post('<?php echo $this->url('action:loadBlockComment') ?>', {'article_id':article_id}, function(d){
                    that.attr('loaded', 1);
                    $('#blockContentArea').html(d);
                });
            }
        });

        $('.reward-flag').on('click', function () {
            var rewardUrl = "<?php echo $this->url('action:reward', array('author_uid' => '::AUTHOR_UID::', 'full' => '::FULL::')) ?>",
                authorUid = $(this).attr('author-uid'),
                full = $(document).width() < 500 ? '1' : '0',
                rewardApiUrl = rewardUrl.replace('::AUTHOR_UID::', authorUid).replace('::FULL::', full);

            $.get(rewardApiUrl, function (d) {
                if (d.status != 1) {
                    layer.msg(d.message);
                } else {
                    layer.open({
                        type: 1,
                        title: false,
                        area: d.data.layer_size,
                        closeBtn: 0,
                        shadeClose: true,
                        content: d.data.img
                    });
                }
            });
        });
    });
</script>



