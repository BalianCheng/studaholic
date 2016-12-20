<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * posts.tpl.php
 */
$images = &$data['images'];
$interact_count = $this->e($data, 'interact_count');
$content_link = $this->url('content:posts', array('posts_id' => $data['posts_id']));
?>
<div class="row">
    <div class="col-md-12">
        <div class="media">

            <div class="media-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="media-heading">
                            <a href="<?php echo $content_link ?>">
                                <?php echo $data['title'] ?>
                            </a>
                        </div>
                        <div class="user-info">
                            <?php
                            echo $this->userNickname($data['account'], $data['nickname'], $data['introduce'], true, array('class' => 'ia')) .
                                $this->xsHideFTime($data['post_time'], ' 于 ');
                            ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-xs-6">
                                <?php echo $this->fCount($interact_count) ?> 回复
                            </div>
                            <div class="col-xs-6">
                                <?php echo $this->fCount($data['posts_hits']) ?> 浏览
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="media-right tac">
                <a title="<?php echo $data['nickname']; ?>"
                   href="<?php echo $this->url('user:detail', array('account' => $data['account'])) ?>">
                    <?php echo $this->userAvatar($data['avatar']) ?>
                </a>
            </div>

        </div>
    </div>
</div>
