<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * index.tpl.php
 */
$page = &$data['page'];
$page_params = $page['link'][1];
$page_params['p'] = ":page:";
$pagingUrl = $this->url($page['link'][0], $page_params);

$pageLessConfig = array(
    'totalPages' => $page['total_page'],
    'currentPage' => $page['p'],
    'url' => $pagingUrl,
    'loaderImage' => $this->res('images/load_content.gif'),
    'loaderMsg' => '内容加载中',
    'endMsg' => '没有更多了'
);
?>
<div class="container">
    <div class="row explore-list">
        <div class="col-md-9">
            <?php if (!empty($data['recommend_content_list'])) : ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-cpf-default content-box">
                            <div class="panel-heading content-menu-title">
                                <img src="<?php echo $this->res('images/recommend.png') ?>" alt="recommend"/>
                                <h4>推荐</h4>
                            </div>
                            <?php $this->contentListSection($data['recommend_content_list'], 'explore/mixed'); ?>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-cpf-default content-box">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-5 content-menu-title">
                                    <img src="<?php echo $this->res('images/explore.png') ?>" alt="explore"/>
                                    <h4>发现</h4>
                                </div>
                                <div class="col-xs-7 heading-right order-menu">
                                    <?php $this->orderMenu('explore:index', array(), $data['order']); ?>
                                </div>
                            </div>
                        </div>
                        <div id="explore-list-wrap">
                            <?php
                            if (!empty($data['content_list'])) {
                                $this->contentListSection($data['content_list'], 'explore/single');
                            } else {
                                echo $this->block('暂无内容', array('style' => 'padding:20px 15px'));
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

        <div class="col-md-3">
            <div class="row col-right">
                <div class="col-md-12">
                    <div class="panel panel-cpf-slide">
                        <div class="panel-heading">
                            <h4>热议</h4>
                        </div>
                        <div class="panel-body">
                            <?php
                            if (!empty($data['recommend_topic'])) {
                                $this->renderTpl('fragment/slide/right_recommend_topic', $data['recommend_topic']);
                            } else {
                                echo $this->block('暂无', array('style' => 'padding:0px'));
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($data['recommend_user'])) : ?>
                <div class="row col-right">
                    <div class="col-md-12">
                        <div class="panel panel-cpf-slide">
                            <div class="panel-heading">
                                <h4>推荐关注TA们</h4>
                            </div>
                            <div class="panel-body">
                                <?php
                                foreach ($data['recommend_user'] as $user) {
                                    $this->renderTpl('fragment/user/recommend_user', $user);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<script src="<?php echo $this->res('libs/jquery_pageless/jquery.pageless.js') ?>"></script>
<script>
    $(function () {
        $('#explore-list-wrap').pageless(<?php echo json_encode($pageLessConfig) ?>);
    })
</script>


