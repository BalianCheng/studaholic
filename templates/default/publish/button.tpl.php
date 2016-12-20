<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * button.tpl.php
 */
$type_name = &$data['type_name'];
$button_txt = array('' => '该话题暂未开放', 'article' => '写文章', 'question' => '提问题', 'posts' => '发帖子');
if($type_name) {
    $publishLink = $this->publishLink($type_name);
} else {
    $publishLink = 'javascript:void(0)';
}
?>
<div class="col-xs-12 btn btn-default btn-publish" id="publish-button">
    <?php echo $button_txt[$type_name] ?>
</div>
<script>
    $('#publish-button').on('click', function () {
        window.location.href = '<?php echo $publishLink ?>';
    })
</script>

