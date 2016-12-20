<form method="post" id="article_form">
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
                        <textarea class="form-control" name="content" id="editor" cols="30" rows="2"></textarea>
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
                        <textarea class="form-control" readonly="readonly"
                                  name="answer" id="editor" cols="30" rows="3"></textarea>
                    </div>
                    <button type="button" class="btn btn-default login-flag">提交</button>
                </div>
            </div>

        <?php endif ?>
    </div>
</form>
<script>
    $(function () {
        $('#article_form').on('submit', function () {
            if (!$('#editor').val()) {
                layer.msg('请输入评论内容');
                return false;
            }
            return true;
        })
    })
</script>
<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * reply_form.tpl.php
 */
