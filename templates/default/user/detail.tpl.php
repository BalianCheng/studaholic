<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * detail.tpl.php
 */
$page = &$data['page'];
$userInfo = &$data['account_info'];
$statistics = &$data['follow_statistics'];
$selfView = $data['uid'] == $userInfo['uid'];
$tab_data = &$data['tab_data'];
?>
<div class="container" style="padding-top:30px;">
    <div class="row">
        <div class="col-md-9 col-centered">
            <div class="row">
                <div class="col-md-12 tac">
                    <?php echo $this->userAvatar($userInfo['avatar'], '96px') ?>
                    <h3><?php echo $userInfo['nickname'] ?></h3>
                    <p>
                        <?php echo $this->e($userInfo, 'introduce') ?>
                    </p>
                </div>
                <div class="col-md-12 tac">
                    <div class="row">
                        <div class="col-xs-6">
                            <div style="float:right">
                                TA关注的
                                <p id="ta_follow"><?php echo $this->e($statistics, 'me_follow_count') ?></p>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div style="float:left">
                                关注TA的
                                <p id="follow_ta"><?php echo $this->e($statistics, 'follow_me_count') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!$selfView) : ?>
                    <div class="col-md-12 tac">
                        <div class="row">
                            <div class="col-xs-6">
                                <div style="float:right">
                                    <?php
                                    if ($data['is_follow']) {
                                        $text = '已关注';
                                    } else {
                                        $text = '关注TA';
                                    }

                                    $attr = array(
                                        '@content' => $text,
                                        'class' => 'btn btn-default btn-current',
                                        'onclick' => "followingUser(this, {$userInfo['uid']})",
                                    );

                                    echo $this->htmlTag('button', $attr);
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div style="float:left">
                                    <button class="btn btn-default btn-current" onclick="message(<?php echo $userInfo['uid'] ?>)">
                                        发私信
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>

            <div class="row tac">
                <div class="col-md-12 user-content-tab">
                    <?php
                    foreach ($tab_data as $action => $name) {
                        if ($action == $data['content_type']) {
                            $class = 'tab col-xs-4 current';
                        } else {
                            $class = 'tab col-xs-4';
                        }

                        $a = $this->wrap('div', array('class' => $class))->html("<h3>{$name}</h3>");
                        echo $this->a($a, $this->url('user:detail', array(
                            'account' => $userInfo['account'],
                            'content_type' => $action
                        )), array('class' => ''));
                    }
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?php
                    if (!empty($data['content_list'])) {
                        $this->contentListSection($data['content_list'], 'user/segment');
                    } else {
                        echo '暂无内容';
                    }
                    ?>

                    <?php echo $this->page($page) ?>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    function followingUser(self, uid) {
        var ta_follow = $('#ta_follow'), follow_ta = $('#follow_ta'),
            apiUrl = '<?php echo $this->url('action:following', array('type' => 'user')) ?>';
        $.post(apiUrl, {'uid': uid, 'get_statistics_info': 1}, function (d) {
            if (d.status == 1) {
                if (d.data.act == 'follow') {
                    $(self).html('已关注');
                } else {
                    $(self).html('关注TA');
                }

                ta_follow.html(d.data.statistics.me_follow_count);
                follow_ta.html(d.data.statistics.follow_me_count);
            } else {
                layer.msg(d.message);
            }
        });
    }
</script>
