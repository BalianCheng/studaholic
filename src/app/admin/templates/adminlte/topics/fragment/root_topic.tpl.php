<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * root_topic.tpl.php
 */
$style = 'padding:5px;border-radius:3px;display:block;text-align:center;';
if($this->data['parent_id'] == $data['topic_id']) {
    $style .= 'background:#3c8dbc;color:#fff;';
}
?>
<li>
    <a style="<?php echo $style ?>" href="<?php echo $this->url('topics:index', array('parent_id' => $data['topic_id'])) ?>">
        <?php echo $data['topic_name'] ?>
    </a>
</li>

