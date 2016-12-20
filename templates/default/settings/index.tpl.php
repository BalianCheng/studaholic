<div class="container" style="margin-top:90px;">
    <div class="row">
        <div class="col-md-12 user-settings">
            <ul class="nav nav-tabs">
                <?php
                foreach($data['tab_config'] as $action => $title) {
                    $controller = 'settings';
                    if($action) {
                        $controller = "settings:{$action}";
                    }

                    $attr = array('role' => 'presentation');
                    if($data['current_tab'] == $action) {
                        $url = 'javascript:void(0)';
                        $attr = array('class' => 'active');
                    } else {
                        $url = $this->url($controller);
                    }

                    echo $this->wrap('li', $attr)->a($title, $url);
                }
                ?>
            </ul>
        </div>
    </div>
</div>

<div class="container" style="margin-bottom: 60px;">
    <div class="row" style="margin-top:20px;">
        <?php $this->renderTpl("settings/fragment/{$data['tpl']}", $data) ?>
    </div>
</div>
