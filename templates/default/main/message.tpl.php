<?php
$dialog_list = &$data['dialog_list'];
$type = &$data['t'];
$page = &$data['page'];

$navTab = array(
    'dialog' => '会话列表',
    'sys' => '系统消息'
)
?>
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="panel panel-cpf-default content-box">
                <div class="panel-heading content-menu-title">
                    <img src="<?php echo $this->res('images/message.png') ?>" alt="message"/>
                    <h4 style="display:inline-block">私信列表</h4>

                    <div class="panel-heading-menu">
                        <?php
                        foreach ($navTab as $t => $name) {
                            if ($t == $type) {
                                $attr['class'] = 'active ia';
                            } else {
                                $attr['class'] = 'ia';
                            }

                            echo $this->a($name, $this->url('main:message', array('t' => $t)), $attr);
                        }
                        ?>
                    </div>
                </div>

                <div class="panel-body" style="padding:15px 0;">
                    <?php
                    if (!empty($dialog_list)) {
                        foreach ($dialog_list as $dialog) {
                            if ($type == 'dialog') {
                                $this->renderTpl('fragment/message/dialog', $dialog);
                            } else {
                                $this->renderTpl('fragment/message/sys', $dialog);
                            }
                        }
                    } else {
                        echo $this->block('暂无消息', array('style' => 'padding:20px 0'));
                    }
                    ?>
                </div>

                <div class="panel-footer">
                    <?php $this->page($page) ?>
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
    $(function () {
        $('.del-message-dialog').on('click', function () {
            var self = $(this), dialog_id = self.attr('dialog-id'),
                dialog_area = '#dialog-area-' + dialog_id;
            $.post('<?php echo $this->url('action:delMessageDialog') ?>', {'dialog_id': dialog_id}, function (d) {
                if (d.status == 1) {
                    $(dialog_area).remove();
                } else {
                    layer.msg(d.message);
                }
            });
        });

        $('.del-message-id').on('click', function () {
            var self = $(this), message_id = self.attr('message-id'),
                message_area = '#message-area-' + message_id;
            $.post('<?php echo $this->url('action:delMessage') ?>', {'message_id': message_id}, function (d) {
                if (d.status == 1) {
                    $(message_area).remove();
                } else {
                    layer.msg(d.message);
                }
            });
        });

    })
</script>
