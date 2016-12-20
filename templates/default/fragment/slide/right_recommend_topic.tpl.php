<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * right_recommend_topic.php
 */
if (!empty($data)) {
    foreach ($data as $d) {
        $topic_link = $this->url('topics:detail', array('topic_url' => $d['topic_url']));
        ?>
        <div class="row topic-slide-list">
            <div class="panel-heading">
                <div class="media">
                    <a class="media-left" href="<?php echo $topic_link ?>">
                        <?php echo $this->topicSigns($d['topic_image'], '42px') ?>
                    </a>
                    <div class="media-body">
                            <a href="<?php echo $topic_link ?>" class="ia">
                                <?php echo $d['topic_name'] ?>
                            </a>
                        <br>
                        <?php echo $this->ee($d, 'follow_count', '0') ?> 人关注
                    </div>
                </div>
            </div>
            <div class="panel-body recommend-body">
                <?php
                if(empty($d['new_content'])) {
                    echo $this->ee($d, 'topic_description', '暂无简介');
                } else {
                    echo $this->simpleTitleUrl($d['new_content']);
                }
                ?>
            </div>
        </div>
        <?php
    }
}
