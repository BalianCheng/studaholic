<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * list.tpl.php
 */
if (!empty($data)) {
    foreach ($data as $d) {
        $topic_link = $this->url('topics:detail', array('topic_url' => $d['topic_url']));
        ?>
        <a href="<?php echo $topic_link ?>" class="ia">
            <span class="topic-small"><?php echo $d['topic_name'] ?></span>
        </a>
        <?php
    }
}
