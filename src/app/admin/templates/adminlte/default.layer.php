<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title><?php echo isset($title) ? $title : 'Admin' ?></title>
    <link rel="stylesheet" href="<?php echo $this->res('libs/bootstrap/3.3.7/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?php echo $this->res("adminlte/2.3.5/dist/css/AdminLTE.min.css") ?>">
    <link rel="stylesheet" href="<?php echo $this->res("adminlte/2.3.5/dist/css/skins/_all-skins.min.css") ?>">
    <link rel="stylesheet"
          href="<?php echo $this->res('adminlte/2.3.5/plugins/font-awesome/4.6.3/css/font-awesome.min.css') ?>">

    <link rel="stylesheet" href="<?php echo $this->res("libs/toggle/2.2.2/css/bootstrap-toggle.min.css") ?>">
    <link rel="stylesheet" href="<?php echo $this->res('libs/nprogress/0.2.0/nprogress.css') ?>">
    <link rel="stylesheet" href="<?php echo $this->res('libs/lightbox/2.8.2/css/lightbox.min.css') ?>">
    <link rel="stylesheet" href="<?php echo $this->res("css/cpf.css") ?>">

    <script src="<?php echo $this->res('libs/jquery/1.11.3/jquery.min.js') ?>"></script>
    <script src="<?php echo $this->res('js/cpa.js') ?>"></script>
    <script type="text/javascript" src="<?php echo $this->res('libs/layer/2.2/layer.js') ?>"></script>
    <script type="text/javascript" src="<?php echo $this->res('libs/layer/2.2/extend/layer.ext.js') ?>"></script>

    <!--[if lt IE 9]>
    <script src="<?php echo $this->res('adminlte/2.3.5/plugins/html5shiv/3.7.3/html5shiv.min.js') ?>"></script>
    <script src="<?php echo $this->res('adminlte/2.3.5/plugins/respond/1.4.2/respond.min.js') ?>"></script>
    <![endif]-->
</head>
<!-- fixed -->
<!-- layout-boxed -->
<!-- sidebar-collapse -->
<!-- skin-blue|black|purple|green|red|yellow|blue-light|black-light|purple-light|green-light|red-light|yellow-light  -->
<body class="hold-transition skin-black sidebar-mini <?php echo $this->e($this->data, 'addClass') ?>">
<div class="wrapper">
    <header class="main-header">
        <a href="" class="logo">
            <span class="logo-mini">
                <img src="<?php echo $this->res("adminlte/2.3.5/dist/img/topic/logo.png") ?>" alt="php framework"
                     style="width:50px;"/>
            </span>
            <span class="logo-lg">
                <img src="<?php echo $this->res("adminlte/2.3.5/dist/img/topic/logo.png") ?>" alt="php framework"
                     style="width:50px;"/>
                <b>S</b>tudaholic
            </span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li id="notifications" class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning" id="msgCount"></span>
                        </a>
                        <ul class="dropdown-menu" id="notifications-list"></ul>
                    </li>
                    <li>
                        <a href="<?php echo $this->url("main:logout") ?>" target="_top">
                            <?php echo $_SESSION['u'] ?> <i class="fa fa-sign-out"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <aside class="main-sidebar">
        <section class="sidebar">
            <ul class="sidebar-menu">
                <li class="header"></li>
                <?php
                $action_menu_name = '';
                $controller_menu_name = '';
                $child_menu_name_map = array();
                $controller = lcfirst($this->controller);

                function li_menu($class, $content, $child_menu = '')
                {
                    if ($child_menu) {
                        return sprintf('<li class="%s">%s%s</li>', $class, $content, $child_menu);
                    } else {
                        return sprintf('<li class="%s">%s</li>', $class, $content);
                    }
                }

                function li_menu_content($link, $target, $icon, $name, $child_node_num)
                {
                    if ($child_node_num > 0) {
                        return sprintf('<a href="%s" target="%s"><i class="%s"></i><span>%s</span><i class="fa fa-angle-left pull-right"></i></a>', $link, $target, $icon, $name);
                    } else {
                        return sprintf('<a href="%s" target="%s"><i class="%s"></i><span>%s</span></a>', $link, $target, $icon, $name);
                    }
                }

                function li_child_menu($class, $link, $target, $icon, $name)
                {
                    return sprintf('<li class="%s"><a href="%s" target="%s"><i class="%s"></i>%s</a></li>', $class, $link, $target, $icon, $name);
                }

                foreach ($this->getAllMenu() as $m) {
                    if ($m['display'] != 1) continue;
                    $icon_name = !empty($m['icon']) ? $m['icon'] : 'fa fa-circle-o';
                    $child_node_num = count($m['child_menu']);

                    $li_class = '';
                    if ($controller == $m['link']) {
                        $controller_menu_name = $m['name'];
                        $li_class = 'active';
                    }

                    if ($child_node_num > 0) {
                        $li_menu = '';
                        foreach ($m['child_menu'] as $mu) {
                            $child_icon_name = !empty($mu['icon']) ? $mu['icon'] : 'fa fa-circle-o';
                            $child_menu_name_map[$m['link']][$mu['link']] = $mu['name'];
                            if ($mu['type'] == 1) {
                                $menu_link = $this->url("{$m['link']}:{$mu['link']}");
                                $li_menu_target = '_self';
                            } else {
                                $menu_link = $mu['link'];
                                $li_menu_target = '_blank';
                            }

                            if ($mu['link'] == $this->action) {
                                $li_menu .= li_child_menu('active', $menu_link, $li_menu_target, $child_icon_name, $mu['name']);
                            } else {
                                $li_menu .= li_child_menu('', $menu_link, $li_menu_target, $child_icon_name, $mu['name']);
                            }
                        }

                        $li_class = "treeview {$li_class}";
                        $child_ul_menu = sprintf('<ul class="treeview-menu">%s</ul>', $li_menu);
                    } else {
                        $child_ul_menu = '';
                    }

                    if ($m['type'] == 1) {
                        $m_link = $this->url($m['link']);
                        $target = '_self';
                    } else {
                        $target = '_blank';
                        $m_link = $m['link'];
                    }

                    echo li_menu($li_class, li_menu_content($m_link, $target, $icon_name, $m['name'], $child_node_num), $child_ul_menu);
                }

                if (isset($child_menu_name_map[$controller]) && isset($child_menu_name_map[$controller][$this->action])) {
                    $action_menu_name = $child_menu_name_map[$controller][$this->action];
                }
                ?>
            </ul>
            <ul class="sidebar-menu">
                <li class="header">其他</li>
                <li>
                    <a href="//imd.ccnu.edu.cn/" target="_blank">
                        <i class="fa fa-circle-o text-aqua"></i>
                        <span>官方支持</span>
                    </a>
                   </li>
                    <li>
                    <a href="//www.studaholic.online/" target="_blank">
                        <i class="fa fa-circle-o text-aqua"></i>
                        <span>网站首页</span>
                    </a>
                </li>
            </ul>
        </section>
    </aside>

    <div class="content-wrapper" id="content-wrapper" style="display: none">
        <section class="content-header">
            <h1>
                <?php
                if ($controller_menu_name) {
                    echo $controller_menu_name;
                }
                if ($action_menu_name) {
                    printf('<small>%s</small>', $action_menu_name);
                }
                ?>
            </h1>
            <ol class="breadcrumb">
                <?php echo $this->getTitleBread() ?>
            </ol>
        </section>

        <section class="content">
            <?php if ($this->data['status'] != 1) : ?>
                <div class="callout callout-info">
                    <h4>提示!</h4>
                    <?php $this->notice($this->data['status'], '%s'); ?>
                </div>
            <?php endif ?>

            <?php echo isset($content) ? $content : ''; ?>
        </section>
    </div>
</div>

<script src="<?php echo $this->res('libs/bootstrap/3.3.7/js/bootstrap.min.js') ?>"></script>
<script src="<?php echo $this->res("libs/toggle/2.2.2/js/bootstrap-toggle.min.js") ?>"></script>
<script src="<?php echo $this->res('adminlte/2.3.5/plugins/slimScroll/jquery.slimscroll.min.js') ?>"></script>
<script src="<?php echo $this->res('adminlte/2.3.5/plugins/fastclick/fastclick.min.js') ?>"></script>
<script src="<?php echo $this->res('adminlte/2.3.5/dist/js/app.min.js') ?>"></script>

<script src="<?php echo $this->res('libs/lightbox/2.8.2/js/lightbox.min.js') ?>"></script>
<script src="<?php echo $this->res('libs/nprogress/0.2.0/nprogress.js') ?>"></script>
<script>
    NProgress.configure({
        template: '<div class="bar" role="bar"><div class="peg"></div></div>'
    });

    NProgress.start();
    $(function () {
        NProgress.done();
        $('#content-wrapper').show();
        $.post('http://imd.ccnu.edu.cn/', <?php echo json_encode($this->version) ?>, function (d) {
            if (d.status == 1) {
                var msgCount = d.message.length, listArea = $('#notifications-list');
                if (msgCount > 0) {
                    $('#notifications').show();
                    $('#msgCount').text(msgCount);
                    $.each(d.message, function (i, v) {
                        var li = $('<li/>');
                        $("<a/>", {href: v['href'], target: '_blank', html: v['title']}).appendTo(li);
                        li.appendTo(listArea);
                    })
                }
            }
        })
    })
</script>
</body>
</html>
