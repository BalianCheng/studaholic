<form method="post" id="contentForm">
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
                        <textarea class="form-control" name="content" id="editor" cols="30" rows="10"></textarea>
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
                        <textarea class="form-control" name="answer" disabled="disabled" readonly="readonly"
                                  id="editor" cols="30" rows="10"></textarea>
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
    $(function () {
        $("img.lazy").each(function () {
            var src = $(this).attr('data-original');
            $(this).attr('src', src);
        });
    });

    editor('editor', '<?php echo $this->url('action:uploadContentImage', array('title_id' => $data['title_id'])); ?>', '<?php echo (int)$data['isLogin'] ?>');
</script>
<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * reply_form.tpl.php
 */
