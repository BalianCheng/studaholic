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

    <!-- Bootstrap -->
    <link href="<?php echo $this->res('libs/bootstrap/3.3.6/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?php echo $this->res('icon/default/icon.css') ?>" rel="stylesheet">
    <link href="<?php echo $this->res('css/main.css') ?>" rel="stylesheet">
    <link href="<?php echo $this->res('theme/cornflower.css') ?>" rel="stylesheet">
    <link href="<?php echo $this->res('css/scrollbar.css') ?>" rel="stylesheet">
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

<div class="defaultWrap">
    <?php
    if (!empty($alertMessage)) {
        echo $this->block($alertMessage, array('class' => 'container', 'style' => 'margin-top:20px;'));
    }

    echo empty($content) ? "" : $content
    ?>
</div>

<script>
    $(function () {
        $('.login-flag').on('click', function () {
            window.location.href = '<?php echo $this->url('user:login') ?>';
        });
    })
</script>
</body>
</html>
