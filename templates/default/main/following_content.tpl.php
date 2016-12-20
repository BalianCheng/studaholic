<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="panel panel-cpf-default content-box">
                <div class="panel-heading content-menu-title">
                    <img src="<?php echo $this->res('images/following.png') ?>" alt="following"/>
                    <h4>我关注的主题</h4>
                </div>

                <div class="panel-body act-list content">
                    <?php
                    if(empty($data['follow_content'])) {
                        echo $this->block('暂无关注主题', array('style' => 'padding:20px 0'));
                    } else {
                        $this->contentListSection($data['follow_content'], 'main/content');
                    }
                    ?>
                </div>

                <div class="panel-footer">
                    <?php echo $this->page($data['page']) ?>
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

