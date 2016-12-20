<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * content_action.tpl.php
 */
$follow_class = 'btn btn-default follow-flag';
$collection_class = 'btn btn-default collection-flag';
if (!empty($data['follow_id'])) {
    $follow_class .= ' btn-current';
    $follow_button_txt = '已关注';
} else {
    $follow_button_txt = '关注';
}

if ($data['collection_id']) {
    $collection_class .= ' btn-current';
    $collection_button_txt = '已收藏';
} else {
    $collection_button_txt = '收藏';
}

$follow_button = $this->htmlTag('button', array(
    '@content' => $follow_button_txt, 'class' => $follow_class, 'title-id' => $data['title_id']
));

$collection_button = $this->htmlTag('button', array(
    '@content' => $collection_button_txt, 'class' => $collection_class, 'title-id' => $data['title_id']
));

$edit_params = $append_params = array(
    'type' => $data['content_type'],
    'content_id' => $data['content_id'],
);

if (isset($data['p'])) {
    $edit_params['p'] = $data['p'];
}

$edit_button = '';
if ($data['can_edit']) {
    $edit_url = $this->url('content:edit', $edit_params);
    $edit_button = $this->a($this->htmlTag('button', array('@content' => '编辑', 'class' => 'btn btn-info')), $edit_url);
}

$append_button = '';
if ($data['can_append']) {
    $append_url = $this->url('content:append', $append_params);
    $append_button = $this->a($this->htmlTag('button', array('@content' => '追加', 'class' => 'btn btn-info')), $append_url);
}

$button_string = implode(' ', array(
    $follow_button, $collection_button, $edit_button, $append_button
));

//是否仅输出按钮
if (!empty($data['onlyButton'])) {
    echo $button_string;
} else {
    ?>
    <div class="panel-cpf-slide">
        <div class="panel-heading">
            <?php echo $button_string ?>
        </div>
        <div class="panel-body">
            <span id="total_follow"><?php echo $data['total_follow'] ?></span> 人关注
        </div>
    </div>
    <?php
}
?>
<script>
    $('.collection-flag').on('click', function () {
        var self = $(this), data = {'title_id': $(this).attr('title-id')};
        $.post('<?php echo $this->url('action:collection') ?>', data, function (d) {
            if (d.status == 1) {
                if (d.data.act == 1) {
                    self.addClass('btn-current');
                    self.html('已收藏');
                } else {
                    self.removeClass('btn-current');
                    self.html('收藏');
                }
            } else {
                layer.msg(d.message);
            }
        });
    });

    $('.follow-flag').on('click', function () {
        var self = $(this), data = {
                'title_id': $(this).attr('title-id'),
                'content_type': <?php echo $data['type'] ?>
            },
            total_ele = $('#total_follow');
        $.post('<?php echo $this->url('action:following', array('type' => 'content')) ?>', data, function (d) {
            if (d.status == 1) {
                if (d.data.act == 1) {
                    self.addClass('btn-current');
                    self.html('已关注');
                } else {
                    self.removeClass('btn-current');
                    self.html('关注');
                }
                total_ele.html(d.data.count);
            } else {
                layer.msg(d.message);
            }
        });
    });
</script>

