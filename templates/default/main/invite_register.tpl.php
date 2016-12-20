<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * invite_register.tpl.php
 */
$invite_count = count($data['inviteUser']);
$preview_url = $this->url('user:invite', array('token' => $data['token']));
?>
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="panel panel-cpf-default content-box">
                <div class="panel-heading content-menu-title">
                    <img src="<?php echo $this->res('images/invite_register.png') ?>" alt="invite"/>
                    <h4>邀请好友</h4>
                </div>

                <div class="panel-body" style="border-bottom: 1px solid #f1f1f1">
                    <div class="row">
                        <div class="col-xs-5">
                            已邀请 <?php echo $invite_count ?> 人
                        </div>
                        <div class="col-xs-7 tar">
                            <a href="<?php echo $preview_url ?>" target="_blank">邀请预览</a>
                            <a href="javascript:void(0)" id="resetInvitePage" class="ia" style="margin-left:5px;">重置</a>
                        </div>
                    </div>
                </div>

                <div class="panel-body">
                    <?php
                    if (empty($data['inviteUser'])) {
                        echo $this->block('暂无', array('style' => 'padding:20px 0'));
                    } else {
                        foreach ($data['inviteUser'] as $d) {
                            $this->renderTpl('fragment/invite/user_list', $d);
                        }
                    }
                    ?>
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
<script type="text/javascript" src="<?php echo $this->res('libs/layer/2.2/layer.js') ?>"></script>
<script>
    $(function () {
        $('#resetInvitePage').on('click', function () {
            layer.msg('之前的邀请连接将失效？', {
                time: 0,
                btn: ['确定', '取消'],
                yes: function (index) {
                    layer.close(index);
                    $.post('<?php echo $this->url('action:resetInviteCode') ?>', function (d) {
                        layer.msg(d.message);
                        if(d.status == 1) {
                            setTimeout(function(){
                                window.location.reload();
                            }, 1000)
                        }
                    })
                }
            })
        })
    })
</script>
