<div class="container">
    <div class="row">
        <div class="col-md-9">
            <?php if ($data['isLogin'] && !empty($data['following_topic'])) : ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-cpf-default content-box">
                            <div class="panel-heading content-menu-title">
                                <img src="<?php echo $this->res('images/following_topics.png') ?>"
                                     alt="following topics"/>
                                <h4>已关注的分类</h4>
                            </div>
                            <div class="panel-body">
                                <div style="padding-top:10px;">
                                    <?php
                                    foreach ($data['following_topic'] as $t) {
                                        $topicSpan = $this->htmlTag('span', array('@content' => $t['topic_name'], 'class' => 'follow-topic-list'));
                                        echo $this->a($topicSpan, $this->url('topics:detail', array('topic_url' => $t['topic_url'])));
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-cpf-default content-box">
                        <div class="panel-heading content-menu-title">
                            <img src="<?php echo $this->res('images/topic.png') ?>" alt="topic"/>
                            <h4>分类列表</h4>
                            <div style="padding-top:10px;">
                                <?php
                                foreach ($data['rootTopics'] as $t) {
                                    $class = 'btn btn-default btn-topic';
                                    if ($t['topic_id'] == $data['parent_topic_id']) {
                                        $class = $class . ' btn-topic-active';
                                    }

                                    $topicSpan = $this->htmlTag('span', array('@content' => $t['topic_name'], 'class' => $class));
                                    echo $this->a($topicSpan, $this->url('topics:index', array('topic_url' => $t['topic_url'])));
                                }
                                ?>
                            </div>
                        </div>

                        <div class="panel-body">
                            <?php
                            if (!empty($data['topicsList'])) {
                                foreach ($data['topicsList'] as $d) {
                                    ?>
                                    <div class="child-topic-list">
                                        <div class="media">
                                            <div class="media-left">
                                                <a href="<?php echo $this->url('topics:detail', array('topic_url' => $d['topic_url'])) ?>">
                                                    <?php echo $this->topicSigns($d['topic_image']) ?>
                                                </a>
                                            </div>
                                            <div class="media-body">
                                                <div class="row">
                                                    <div class="col-xs-7 col-lg-10">
                                                        <h5 class="media-heading">
                                                            <a href="<?php echo $this->url('topics:detail', array('topic_url' => $d['topic_url'])) ?>">
                                                                <?php echo $d['topic_name'] ?>
                                                            </a>
                                                        </h5>
                                                    </div>
                                                    <div class="col-xs-5 col-lg-2 text-right">
                                                        <?php
                                                        if ($d['is_following']) {
                                                            $url = $this->url('topics:unFollowing', array('topic_url' => $data['topic_url'], 'topic_id' => $d['topic_id']));
                                                            $spanAttr = array('class' => '', '@content' => '取消关注');
                                                        } else {
                                                            $url = $this->url('topics:following', array('topic_url' => $data['topic_url'], 'topic_id' => $d['topic_id']));
                                                            $spanAttr = array('class' => '', '@content' => '关注分类');
                                                        }

                                                        echo $this->a($this->htmlTag('span', $spanAttr), $url);
                                                        ?>
                                                    </div>
                                                    <div class="col-md-12 topic-description">
                                                        <?php echo !empty($d['topic_description']) ? $this->substr($d['topic_description'], 60) : '暂无描述'; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo $this->block('暂无分类', array('style' => 'padding:5px'));
                            }
                            ?>
                        </div>

                        <div class="panel-footer">
                            <?php echo $this->page($data['page']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-right">
            <div class="panel-cpf-slide">
                <div class="panel-heading">
                    <h4>推荐关注</h4>
                </div>
                <div class="panel-body">
                    <?php
                    if (!empty($data['recommend_topic'])) {
                        foreach ($data['recommend_topic'] as $d) {
                            if ($d['is_following']) {
                                $topic_name = $this->htmlTag('span', array('class' => 'current', '@content' => $d['topic_name']));
                                $url = $this->url('topics:unFollowing', array('topic_url' => $data['topic_url'], 'topic_id' => $d['topic_id']));
                                $action = $this->a('取消关注', $url, array(
                                    'style' => 'line-height:34px;'
                                ));
                            } else {
                                $topic_name = $this->htmlTag('span', array('class' => '', '@content' => $d['topic_name']));
                                $url = $this->url('topics:following', array('topic_url' => $data['topic_url'], 'topic_id' => $d['topic_id']));
                                $action = $this->a('关注分类', $url, array(
                                    'style' => 'line-height:34px;',
                                    'class' => 'ia'
                                ));
                            }

                            $topic_url = $this->url('topics:detail', array('topic_url' => $d['topic_url']));
                            ?>
                            <div class="row" style="padding:5px 0;">
                                <div class="col-xs-7">
                                    <div class="media">
                                        <a class="media-left" href="#">
                                            <?php echo $this->topicSigns($d['topic_image'], '42px') ?>
                                        </a>
                                        <div class="media-body">
                                            <a href="<?php echo $topic_url ?>" class="ia">
                                                <?php echo $topic_name ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-5 tac">
                                    <?php echo $action ?>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo $this->block('暂无推荐', array('style' => 'padding:0px'));
                    }
                    ?>
                </div>
            </div>
        </div>

    </div>
</div>
<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * index.tpl.php
 */
