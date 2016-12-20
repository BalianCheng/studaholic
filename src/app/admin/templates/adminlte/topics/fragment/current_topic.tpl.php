<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * current_topic.php
 */
$topic_id = &$data['topic_id'];
?>
<tr>
    <td><?php echo $topic_id ?></td>
    <td>
        <input class="form-control" name="<?php echo $topic_id ?>[topic_name]" type="text" value="<?php echo $this->e($data, 'topic_name') ?>">
    </td>
    <td>
        <input class="form-control" name="<?php echo $topic_id ?>[topic_url]" type="text" value="<?php echo $this->e($data, 'topic_url') ?>">
    </td>
    <td>
        <label class="checkbox-inline">
            <input type="checkbox" name="<?php echo $topic_id ?>[as_recommend]" data-toggle="toggle" data-on="是" data-off="否"
                   data-size="small" <?php if ($this->e($data, 'as_recommend')) echo 'checked' ?>>
        </label>
    </td>
    <td>
        <label class="checkbox-inline">
            <input type="checkbox" name="<?php echo $topic_id ?>[enable_question]" data-toggle="toggle" data-on="开启" data-off="关闭"
                   data-size="small" <?php if ($this->e($data, 'enable_question')) echo 'checked' ?>>
        </label>
    </td>
    <td>
        <label class="checkbox-inline">
            <input type="checkbox" name="<?php echo $topic_id ?>[enable_posts]" data-toggle="toggle" data-on="开启" data-off="关闭"
                   data-size="small" <?php if ($this->e($data, 'enable_posts')) echo 'checked' ?>>
        </label>
    </td>
    <td>
        <label class="checkbox-inline">
            <input type="checkbox" name="<?php echo $topic_id ?>[enable_article]" data-toggle="toggle" data-on="开启" data-off="关闭"
                   data-size="small" <?php if ($this->e($data, 'enable_article')) echo 'checked' ?>>
        </label>
    </td>
    <td>
        <input class="form-control" name="<?php echo $topic_id ?>[sort]" type="text" value="<?php echo $this->e($data, 'sort') ?>">
    </td>
    <td>
        <a href="javascript:void(0)" class="edit-topic-flag" topic-id="<?php echo $topic_id ?>">编辑</a>
        <a href="javascript:void(0)" class="topic-manager-flag" topic-id="<?php echo $topic_id ?>">设置编辑</a>
        <a href="javascript:void(0)" class="del-topic-flag" topic-id="<?php echo $topic_id ?>">删除</a>
    </td>
</tr>

