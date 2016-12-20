<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?php echo $this->getSeoInfo('title') ?></title>
    <meta name="Keywords" content="<?php echo $this->getSeoInfo('keywords') ?>"/>
    <meta name="Description" content="<?php echo $this->getSeoInfo('description') ?>"/>
	<link rel="shortcut icon" type="image/x-icon" href="http://www.studaholic.online/favicon.ico" media="screen" />    
    <!-- Bootstrap -->
    <link href="<?php echo $this->res('libs/bootstrap/3.3.6/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?php echo $this->res('icon/default/icon.css') ?>" rel="stylesheet">
    <link href="<?php echo $this->res('css/main.css') ?>" rel="stylesheet">
    <link href="<?php echo $this->res('theme/cornflower.css') ?>" rel="stylesheet">
    <?php echo $this->loadRes('header') ?>

    <!--[if lt IE 9]>
    <script src="<?php echo $this->res('libs/html5shiv/3.7.2/html5shiv.min.js') ?>"></script>
    <script src="<?php echo $this->res('libs/respond.js/1.4.2/respond.min.js') ?>"></script>
    <![endif]-->

    <script src="<?php echo $this->res('libs/jquery/1.11.1/jquery.min.js') ?>"></script>
    <script src="<?php echo $this->res('libs/bootstrap/3.3.6/js/bootstrap.min.js') ?>"></script>
    <script src="<?php echo $this->res('libs/layer/2.2/layer.js') ?>"></script>
</head>
<body>

<div id="topNav" class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <?php if ($this->data['isLogin']) : ?>
                <a href="javascript:void(0)"
                   style="padding:7px 15px;" type="button" class="navbar-toggle xs-navbar-toggle visible-xs"
                   data-toggle="collapse"
                   data-target=".navbar-collapse">
                    <?php echo $this->userAvatar($this->data['loginUser']['avatar'], '36px') ?>
                    <span class="nickname"><?php echo $this->data['loginUser']['nickname'] ?></span>
                </a>
            <?php else : ?>
                <span class="btn-group btn-group-theme navbar-toggle xs-navbar-toggle visible-xs" role="group">
                    <a href="<?php echo $this->url('user:login') ?>" class="btn btn-default ">登录</a>
                    <a href="<?php echo $this->url('user:register') ?>" class="btn btn-default ">注册</a>
                </span>
            <?php endif ?>

            <a class="navbar-brand" href="<?php echo $this->url() ?>">
                <img src="<?php echo $this->res('images/topics/navbar_logo.png') ?>" alt="logo"/>
            </a>
        </div>

        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav hidden-xs">
                <?php $this->contentNavMenu() ?>
            </ul>

            <?php if ($this->data['isLogin']) : ?>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown user user-menu hidden-xs">
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"
                           style="padding:7px 15px;">
                            <?php echo $this->userAvatar($this->data['loginUser']['avatar'], '36px') ?>
                            <span><?php echo $this->data['loginUser']['nickname'] ?></span>
                        </a>

                        <ul class="dropdown-menu">
                            <?php $this->renderTpl('user/nav_menu') ?>
                        </ul>
                    </li>
                </ul>

                <ul class="nav navbar-nav navbar-right visible-xs">
                    <?php $this->renderTpl('user/nav_menu') ?>
                </ul>
            <?php else : ?>
                <ul class="nav navbar-nav navbar-right hidden-xs" style="padding-left:15px;">
                    <li>
                        <span class="btn-group btn-group-theme" role="group">
                            <a href="<?php echo $this->url('user:login') ?>" class="btn btn-default ">登录</a>
                            <a href="<?php echo $this->url('user:register') ?>" class="btn btn-default ">注册</a>
                        </span>
                    </li>
                </ul>
            <?php endif ?>

            <form id="search-form" class="navbar-form navbar-right hidden-xs" role="search">
                <div class="input-group">
                    <input type="text" class="form-control search-input" placeholder="搜索关键词">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-default">
                            <span class="glyphicon glyphicon-search" style="color:#ccc" aria-hidden="true"></span>
                        </button>
                   </span>
                </div>
            </form>

            <ul class="nav navbar-nav navbar-right hidden-xs">
                <li class="dropdown">
                    <?php if ($this->data['isLogin']) : ?>
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true"
                           aria-expanded="false">
                            发布
                        </a>
                        <ul class="dropdown-menu">
                            <?php $this->publishMenu() ?>
                        </ul>
                    <?php else: ?>
                        <a href="<?php echo $this->url('user:login') ?>" class="dropdown-toggle">发布</a>
                    <?php endif ?>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="navbar navbar-xs-bottom navbar-fixed-bottom visible-xs">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul class="nav navbar-nav navbar-xs-ul" style="display: inline-block">
                    <?php $this->contentNavMenu('li', true) ?>
                </ul>
                <ul class="nav navbar-nav navbar-right navbar-xs-ul" style="display: inline-block;float:right">
                    <li>
                        <?php if ($this->data['isLogin']) : ?>
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#xs-publish-modal"
                               class="dropdown-toggle">发布</a>
                        <?php else: ?>
                            <a href="<?php echo $this->url('user:login') ?>" class="dropdown-toggle">发布</a>
                        <?php endif ?>
                    </li>
                    <li>
                        <a data-toggle="modal" data-target="#xs-search-form-modal">搜索</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- xs search Modal -->
<div class="modal fade" id="xs-search-form-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <form id="xs-search-form" role="search">
        <div class="container xs-search">
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group">
                        <input type="text" class="form-control input-lg search-input" placeholder="搜索关键词">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-lg btn-default">
                             <span class="glyphicon glyphicon-search" style="color:#ccc"
                                   aria-hidden="true"></span>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal fade" id="xs-publish-modal" tabindex="-1" role="form">
    <div class="container xs-publish">
        <div class="row col-centered tac">
            <?php $this->publishMenu('xs') ?>
        </div>
    </div>
</div>

<div class="mainWrap">
    <?php
    if (!empty($alertMessage)) {
        echo $this->block($alertMessage, array('class' => 'container', 'style' => 'margin-top:60px;'));
    }

    echo empty($content) ? "" : $content
    ?>
</div>

<div class="footWrap">
    <div class="container">
        <div class="row hidden-xs hidden-sm">
            <div class="col-md-12 tac">
                Powered by <a href="//www.codingbalian.online/" class="ia" target="_blank"><b>BalianCheng</b></a>
                <?php echo $this->data['version'] ?>
            </div>
        </div>
        <div class="row" style="position:relative">
            <div id="goTop">
                <img src="<?php echo $this->res('images/gotop.png') ?>" border="0" alt="gotop"/>
            </div>
            <div id="newMessage" class="new-message-flag">
                <span class="badge new-message-badge new-message-count-flag">0</span>
                <img src="<?php echo $this->res('images/new_message.png') ?>" border="0" alt="new message tips">
            </div>

            <div id="titleList">
                <div id="titleListContent" class="hidden-xs">
                    <div id="af" style="width:500px;"></div>
                </div>
                <img src="<?php echo $this->res('images/list.png') ?>" border="0" alt="title list">
            </div>
        </div>
    </div>
</div>
<script src="<?php echo $this->res('libs/jquery_lazyload/1.9.7/jquery.lazyload.min.js') ?>"></script>
<script src="<?php echo $this->res('js/jquery.bootstrap-autohidingnavbar.min.js') ?>"></script>
<script>
    function message(uid) {
        var url = '<?php echo $this->url('message:with', array('uid' => '::UID::')) ?>',
            area_width = $(document).width() < 860 ? '80%' : '43%';

        layer.open({
            type: 2,
            title: '',
            shadeClose: true,
            shade: 0.6,
            area: [area_width, '80%'],
            content: url.replace('::UID::', uid)
        });
    }

    $.jheartbeat = {
        options: {delay: 1},
        beatFunction: function () {
        },
        timeoutObj: {id: -1},
        set: function (options, onBeatFunction) {
            if (this.timeoutObj.id > -1) {
                clearTimeout(this.timeoutObj);
            }
            if (options) {
                $.extend(this.options, options);
            }
            if (onBeatFunction) {
                this.beatFunction = onBeatFunction;
            }

            this.timeoutObj.id = setTimeout("$.jheartbeat.beat();", 1000 * this.options.delay);
        },

        beat: function () {
            this.timeoutObj.id = setTimeout("$.jheartbeat.beat();", 1000 * this.options.delay);
            this.beatFunction();
        }
    };

    $(function () {

        $("img.lazy").lazyload();

        $(".navbar-fixed-top").autoHidingNavbar();

        $('.login-flag').on('click', function () {
            window.location.href = '<?php echo $this->url('user:login') ?>';
        });

        $('#goTop').click(function () {
            $("html, body").animate({scrollTop: 0}, 200);
        });

        var search_url = '<?php echo $this->url('search:index', array('q' => '::Q::')) ?>';
        $('#search-form, #xs-search-form').on('submit', function () {
            var q = $(this).find('.search-input').val();
            if (!q) {
                layer.msg('请输入关键词');
            } else {
                window.location.href = search_url.replace('::Q::', q);
            }

            return false;
        });

        <?php if($this->data['isLogin']) : ?>
        var newMessage = $('.new-message-flag'), newMessageCount = $('.new-message-count-flag');
        $.jheartbeat.set({delay: 30}, function () {
            $.get('<?php echo $this->url('action:getNewMessageCount') ?>', function (d) {
                if (d.data.count > 0) {
                    newMessage.show();
                    newMessageCount.html(d.data.count);
                }
            })
        });
        newMessage.on('click', function () {
            location.href = '<?php echo $this->url('main:message') ?>';
        });
        <?php endif ?>

        var af = [], caf = window.location.hash.substring(1), nm = $('#newMessage');
        <?php if($this->controller == 'Content') : ?>
        $('.article-body').find('h3,h4').each(function (i, ele) {
            if ($(this).text() != '') {
                var data = {'name': $(this).text(), 'id': $(this).attr('id')};
                if (ele.tagName == 'H3') {
                    data.child = [];
                    af.push(data);
                } else {
                    af[af.length - 1].child.push(data)
                }
            }
        });
        <?php endif ?>

        if (af.length > 0) {
            var tl = $('#titleList'), tlc = $('#titleListContent');
            tl.css({'bottom': '117px'});
            nm.css({'bottom': '155px'});
            tl.show();
            document.getElementById('af').innerHTML = template('affixTpl', {'af': af, 'caf': caf});
            tl.find('img').on('click', function () {
                tlc.toggle();
            });
        } else {
            nm.css({'bottom': '117px'});
        }

        var lastScrollTop = 0;
        $(window).bind("scroll", function () {
            var st = $(document).scrollTop();
            if (st > 150) {
                $('#goTop').show();
            } else {
                $('#goTop').hide();
            }

            lastScrollTop = st;
        });
    })
</script>
</body>
</html>
