<form method="post">
    <div class="editor-user-info">
        <?php if ($data['isLogin']): ?>

            <div class="row info">
                <div class="col-xs-9">
                    <?php echo $this->loginUserNickname($data['loginUser']); ?>
                </div>
                <div class="col-xs-3 tar">
                    <?php echo $this->userAvatar($data['loginUser']['avatar'], '24px') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <textarea class="form-control" name="content" id="editor" cols="30"
                                  rows="10"><?php echo $this->e($data, 'content') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </div>

        <?php else : ?>

            <div class="row info">
                <div class="col-xs-12">
                    请先
                    <a href="<?php echo $this->url('user:login') ?>">登录</a>
                    或
                    <a href="<?php echo $this->url('user:register') ?>">注册</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <textarea class="form-control" disabled="disabled" readonly="readonly"
                                  name="answer" id="editor" cols="30" rows="10"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary login-flag">提交</button>
                </div>
            </div>

        <?php endif ?>
    </div>
</form>
<link rel="stylesheet" href="<?php echo $this->res('libs/wangeditor/2.1.20/css/wangEditor.min.css') ?>">
<script src="<?php echo $this->res('libs/wangeditor/2.1.20/js/wangEditor.min.js') ?>"></script>
<script src="<?php echo $this->res('js/editor.js') ?>"></script>
<script>
    var editor = editor('editor', '<?php echo $this->url('action:uploadImage'); ?>', '<?php echo (int)$data['isLogin'] ?>');
    $(function () {

        $("img.lazy").each(function () {
            var src = $(this).attr('data-original');
            $(this).attr('src', src);
        });

        $("form").on('submit', function () {
            var content = editor.$txt, index = 0;
            content.find('h3,h4').each(function () {
                if ($(this).text()) {
                    index++;
                    $(this).attr('id', 't' + index);
                } else {
                    $(this).removeAttr('id');
                }
            });

            $('#editor').val(content.html());
        });
    });
</script>
<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * main_form.tpl.php
 */
