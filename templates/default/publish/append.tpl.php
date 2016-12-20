<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * question.tpl.php
 */

$type = &$data['type'];
$content = &$data['content'];

$addition_data = $content;
$addition_data['isLogin'] = $data['isLogin'];
$addition_data['loginUser'] = $data['loginUser'];

//不同类型的私有id和值
$content_id = 0;
$content_name = "{$type}_id";
if (isset($content[$content_name])) {
    $content_id = $content[$content_name];
}

$p = !empty($content['p']) ? (int)$content['p'] : 1;
$edit_url = $this->url("content:edit", array('type' => $type, 'content_id' => $content_id, 'p' => $p));
$content_url = $this->url("content:{$type}", array($content_name => $content_id));
?>
<div class="container publish">
    <div class="row">
        <div class="col-md-12">
            <form id="publishForm" method="post"
                  action="<?php echo $this->url('publish:append', array('type' => $type, 'csrf_token' => $this->e($data, 'csrf_token'))) ?>">
                <div class="form-group">
                    <label for="title"><?php echo $this->e($data, 'title') ?></label>
                    <input type="hidden" name="title_id" value="<?php echo $this->e($content, 'title_id') ?>">
                    <input type="hidden" name="<?php echo $content_name ?>" value="<?php echo $content_id ?>">
                    <label for="title">
                        标题
                        <a href="<?php echo $content_url ?>" target="_blank" class="label-action">查看</a>
                        <a href="<?php echo $edit_url ?>" target="_blank" class="label-action">编辑</a>
                    </label>
                    <div class="form-control-static" id="title">
                        <h4>
                            <?php echo $this->e($content, 'title') ?>
                        </h4>
                    </div>
                </div>

                <?php $this->renderTpl("publish/append/{$type}", $addition_data); ?>
            </form>
        </div>
    </div>
</div>
<script>
    $(function () {

        $('#content-fold').on('click', function () {
            var op = $(this).attr('op');
            if (op == 0) {
                $(this).html('折叠').attr('op', 1);
                $('.content-fold-flag').css({"max-height": "100%"});
            } else {
                $(this).html('展开').attr('op', 0);
                $('.content-fold-flag').css({"max-height": "70px"});
            }
        });

        $('#publishForm').on('submit', function () {
            var content = editor.$txt.text();
            if (!content) {
                layer.msg("内容不能为空");
                return false;
            }

            return true;
        });
    });
</script>
