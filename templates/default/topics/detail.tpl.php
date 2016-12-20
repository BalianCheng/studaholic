<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * detail.tpl.php
 */
//print_r($data);
$topic_info = &$data['topic_info'];
$follow_info = &$data['follow_info'];

if ($follow_info['is_following']) {
    $url = $this->url('topics:unFollowing', array('topic_url' => $data['topic_url'], 'topic_id' => $topic_info['topic_id']));
    $buttonAttr = array('class' => 'btn btn-default btn-current', '@content' => '取消关注');
} else {
    $url = $this->url('topics:following', array('topic_url' => $data['topic_url'], 'topic_id' => $topic_info['topic_id']));
    $buttonAttr = array('class' => 'btn btn-default', '@content' => '关注话题');
}
?>
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-12">
                    <div class="media topic-main">
                        <div class="media-left topic-signs">
                            <?php echo $this->topicSigns($topic_info['topic_image'], '90px') ?>
                        </div>
                        <div class="media-body">
                            <div class="topic-detail-name">
                                <?php echo $topic_info['topic_name'] ?>
                            </div>

                            <div class="topic-description">
                                <?php echo empty($topic_info['topic_description']) ? '暂无简介' : ($topic_info['topic_description']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="order-menu text-right topic-order">
                        <?php $this->orderMenu('topics:detail', array('topic_url' => $data['topic_url'], 'type' => $data['type_name']), $data['order']); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 content-type-list">
                    <ul class="nav nav-tabs nav-tabs-contentType">
                        <?php $this->contentTypeMenu($data['topic_public_status'], $topic_info['topic_url'], $data['type_name']) ?>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="panel-body content-list-wrap content">
                    <?php
                    if (!empty($data['content'])) {
                        $this->contentListSection($data['content'], 'topics/segment', array('class' => 'posts-list'));
                    } else {
                        $this->renderTpl('publish/button', $data);
                    }
                    ?>
                </div>
                <div class="panel-footer">
                    <?php echo $this->page($data['page']); ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="row col-right">
                <div class="col-md-12">
                    <div class="panel panel-cpf-slide">
                        <div class="panel-heading">
                            <?php echo $this->a($this->htmlTag('button', $buttonAttr), $url); ?>
                        </div>

                        <div class="panel-body">
                            <?php echo $follow_info['following_count'] ?> 人关注
                        </div>
                    </div>
                </div>
            </div>

            <div class="row col-right">
                <div class="col-md-12">
                    <div class="panel panel-cpf-slide">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-sm-8 recommend-title">相关话题</div>
                                <div class="col-sm-3 recommend-more">
                                    <small>
                                        <a href="<?php echo $this->url('topics:index', array('topic_url' => $topic_info['parent_topic_url'])) ?>">
                                            更多
                                        </a>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="panel-body">
                            <?php
                            if (!empty($data['related_topics'])) {
                                foreach ($data['related_topics'] as $t) {
                                    $span = $this->htmlTag('span', array(
                                        '@content' => $t['topic_name'],
                                        'class' => 'btn btn-default btn-topic'
                                    ));
                                    echo $this->a($span, $this->url('topics:detail', array('topic_url' => $t['topic_url'])));
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

