<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * index.tpl.php
 */
$page = $data['page'];
$page_params = $page['link'][1];
$page_params['p'] = ":page:";
$pagingUrl = $this->url($page['link'][0], $page_params);
$act_list_count = count($data['act_list']);
$show_recommend_user = $act_list_count < 10 ? true : false;

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
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-cpf-default content-box">
                        <div class="panel-heading content-menu-title">
                            <img src="<?php echo $this->res('images/act.png') ?>" alt="act"/>
                            <h4>动态</h4>
                        </div>

                        <div id="act-list-wrap" class="panel-body act-list">
                            <?php
                            if (!empty($data['act_list'])) {
                                $this->showAct($data['act_list']);
                            }

                            if($show_recommend_user) {
                                $this->renderTpl('main/empty_act_list', $act_list_count);
                            }
                            ?>
                        </div>

                        <div class="panel-footer">
                            <?php $this->page($data['page']) ?>
                        </div>
                    </div>
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
<script src="<?php echo $this->res('libs/jquery_pageless/jquery.pageless.js') ?>"></script>
<script>
    $(function () {
        $('#act-list-wrap').pageless(<?php echo json_encode($pageLessConfig) ?>);
    })
</script>
