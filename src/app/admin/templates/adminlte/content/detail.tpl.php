<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * detail.tpl.php
 */
//print_r($data);
$contentList = &$data['info']['content_list'];
?>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span></button>
        </div>
        <div class="modal-body">
            <?php
            foreach ($contentList as $content) {
                echo $this->wrap('p')->html($content['content']);
            }
            ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
