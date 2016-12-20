<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * reply.tpl.php
 */
$contentNav = array('block' => '已屏蔽', 'hidden' => '已折叠', 'list' => '正常');
?>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs pull-right">
        <?php
        foreach ($contentNav as $navName => $navTxt) {
            if ($this->params['t'] == $navName) {
                $wrap = $this->wrap('li', array('class' => 'active'));
            } else {
                $wrap = $this->wrap('li');
            }

            echo $wrap->a($navTxt, $this->url("interact:reply", array('t' => $navName)));
        }
        ?>
        <li class="pull-left header">
            <i class="fa fa-th"></i>帖子回复
        </li>
    </ul>

    <div class="tab-content table-responsive">
        <div class="box-body ">
            <?php if (!empty($data['list'])) : ?>
                <table class="table table-bordered table-hover">
                    <tr>
                        <th style="width:60px;min-width:60px;">ID</th>
                        <th style="width:60px;min-width:60px;">帖子ID</th>
                        <th style="width:100px;min-width:100px;">发布者</th>
                        <th>内容</th>
                        <th style="width:100px;max-width:100px;">发布者IP</th>
                        <th style="width:180px;min-width:180px;">发布时间</th>
                        <th style="width:138px;min-width:138px;">操作</th>
                    </tr>
                    <?php foreach ($data['list'] as $d) : ?>
                        <tr>
                            <td><?php echo $d['reply_id'] ?></td>
                            <td>
                                <a href="<?php echo $this->url('forum:jumpToContent', array('title_id' => $d['title_id'])) ?>"
                                   target="_blank">
                                    <?php echo $d['posts_id'] ?>
                                </a>
                            </td>
                            <td><?php echo $d['nickname'] ?></td>
                            <td style="min-width:260px;max-width:300px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
                                <?php echo $this->imagesToLink($d['reply_content'], $d['title_id']) ?>
                            </td>
                            <td><?php echo long2ip($d['reply_ip']) ?></td>
                            <td><?php echo date('Y-m-d H:i:s', $d['reply_time']) ?></td>
                            <td>
                                <?php
                                switch ($this->params['t']) {
                                    case 'hidden':
                                        $cancelHiddenAct = $this->url('interact:changeStatus', array('type' => 'reply', 'id' => $d['reply_id'], 'status' => 1));
                                        echo $this->a('取消折叠', $cancelHiddenAct);

                                        $blockAct = $this->url('interact:changeStatus', array('type' => 'reply', 'id' => $d['reply_id'], 'status' => -2));
                                        echo $this->a('屏蔽', $blockAct);
                                        break;

                                    case 'block':
                                        $cancelBlockAct = $this->url('interact:changeStatus', array('type' => 'reply', 'id' => $d['reply_id'], 'status' => 1));
                                        echo $this->a('取消屏蔽', $cancelBlockAct);

                                        $hiddenAct = $this->url('interact:changeStatus', array('type' => 'reply', 'id' => $d['reply_id'], 'status' => -1));
                                        echo $this->a('折叠', $hiddenAct);
                                        break;

                                    case 'list':
                                    default:
                                        $hiddenAct = $this->url('interact:changeStatus', array('type' => 'reply', 'id' => $d['reply_id'], 'status' => -1));
                                        echo $this->a('折叠', $hiddenAct);

                                        $blockAct = $this->url('interact:changeStatus', array('type' => 'reply', 'id' => $d['reply_id'], 'status' => -2));
                                        echo $this->a('屏蔽', $blockAct);
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </table>
            <?php else : ?>
                <div>暂无</div>
            <?php endif ?>
        </div>
        <div class="box-footer">
            <?php echo $this->page($data['page']) ?>
        </div>
    </div>
</div>
