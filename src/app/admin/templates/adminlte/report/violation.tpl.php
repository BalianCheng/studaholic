<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * violation.tpl.php
 */
?>
<div class="box table-responsive">
    <div class="box-header with-border">
        <h3 class=box-title>举报内容</h3>
    </div>

    <div class="box-body">
        <?php if (!empty($data['report_list'])) : ?>
            <table class="table table-bordered table-hover">
                <tr>
                    <th style="width:80px;min-width:80px;">内容ID</th>
                    <th style="width:80px;min-width:80px;">类型</th>
                    <th style="width:150px;min-width:100px;">举报用户</th>
                    <th style="min-width:300px;">内容</th>
                    <th style="width:180px;min-width:180px;">举报时间</th>
                    <th style="width:120px;min-width:120px;">操作</th>
                </tr>

                <?php
                foreach ($data['report_list'] as $report) {
                    ?>
                    <tr>
                        <td><?php echo $report['report_id'] ?></td>
                        <td><?php echo $report['interact_type'] ?></td>
                        <td><?php echo $report['report_nickname'] ?></td>
                        <td><?php echo $report['report_content'] ?></td>
                        <td><?php echo date('Y-m-d H:i:s', $report['rt']) ?></td>
                        <td>
                            <a href="<?php echo $this->url('report:action', array('type' => 'hide', 'id' => $report['id'])) ?>">折叠</a>
                            <a href="<?php echo $this->url('report:action', array('type' => 'block', 'id' => $report['id'])) ?>">屏蔽</a>
                            <a href="<?php echo $this->url('report:ignore', array('id' => $report['id'])) ?>">忽略</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        <?php else : ?>
            <div>暂无举报类容</div>
        <?php endif ?>
    </div>

    <div class="box-footer">
        <?php $this->page($data['page']) ?>
    </div>
</div>
