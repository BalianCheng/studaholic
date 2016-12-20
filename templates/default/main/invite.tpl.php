<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="panel panel-cpf-default content-box">
                <div class="panel-heading content-menu-title">
                    <img src="<?php echo $this->res('images/invite.png') ?>" alt="invite"/>
                    <h4>邀请我参与的主题</h4>
                    <div class="panel-heading-menu">
                        <?php
                        foreach ($data['filter_type_config'] as $t => $name) {
                            if ($this->params['t'] == $t) {
                                $attr['class'] = 'active ia';
                            } else {
                                $attr['class'] = 'ia';
                            }
                            echo $this->a($name, $this->url('main:invite', array('t' => $t)), $attr);
                        }
                        ?>
                    </div>
                </div>

                <div class="panel-body act-list content">
                    <?php
                    if (empty($data['invite_content'])) {
                        echo $this->block('暂无', array('style' => 'padding:20px 0'));
                    } else {
                        $this->contentListSection($data['invite_content'], 'main/inviteList');
                    }
                    ?>
                </div>

                <div class="panel-footer">
                    <?php echo $this->page($data['page']) ?>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-main-right">
            <div class="main-side">
                <ul class="nav nav-pills nav-side nav-stacked">
                    <?php $this->renderTpl('fragment/slide/main_menu', $this->main_slide_menu) ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    $('.ignore-flag').on('click', function () {
        var invite_id = $(this).attr('invite_id');
        $.post('<?php echo $this->url('action:ignoreInvite') ?>', {'invite_id': invite_id}, function (d) {
            if (d.status == 1) {
                layer.msg('操作成功!');
                setTimeout(function () {
                    window.location.reload();
                }, 1000);
            } else {
                layer.msg(d.message);
            }
        });
    })
</script>
