<?php
/**
 * @Auth wonli <wonli@live.com>
 * detail.tpl.php
 */
$page = $data['page'];
$editor_data = array('isLogin' => $data['isLogin'], 'loginUser' => $data['loginUser']);

$page_params = $page['link'][1];
$page_params['p'] = ":page:";
$pagingUrl = $this->url($page['link'][0], $page_params);

$pageLessConfig = array(
    'totalPages' => $page['total_page'],
    'currentPage' => $page['p'],
    'url' => $pagingUrl,
    'loaderImage' => $this->res('images/load_content.gif'),
    'loaderMsg' => '内容加载中',
    'endMsg' => '没有更多了',
);
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php $this->renderTpl('fragment/editor/simple_form', $data) ?>
        </div>
    </div>

    <div class="row">

        <div class="col-md-12">
            <div class="row message-list-info">
                <?php echo $page['result_count'] ?> 条消息
            </div>
        </div>

        <div class="col-md-12" id="message_list">
            <?php
            if (!empty($data['message_list'])) {
                $this->dialogMessageList($data['message_list']);
            } else {
                echo '暂无消息';
            }
            ?>
        </div>
    </div>
</div>
<script src="<?php echo $this->res('libs/jquery_pageless/jquery.pageless.js') ?>"></script>
<script>
    $(function () {

        $('#message_list').pageless(<?php echo json_encode($pageLessConfig) ?>);

        $('.message-content').hover(function () {
            $(this).find('button').show();
        }, function () {
            $(this).find('button').hide();
        });

        $('.message-del-flag').on('click', function () {
            var self = $(this), message_id = self.attr('message-id'),
                message_content_area = '#message-content-area-' + message_id;
            $.post('<?php echo $this->url('action:delMessage') ?>', {'message_id':message_id}, function(d) {
                if(d.status == 1) {
                    $(message_content_area).remove();
                } else {
                    layer.msg(d.message);
                }
            });
        });
    });

</script>


