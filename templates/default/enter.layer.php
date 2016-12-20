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
    <link href="<?php echo $this->res('css/main.css') ?>" rel="stylesheet">
    <link href="<?php echo $this->res('css/enter.css') ?>" rel="stylesheet">
    <link href="<?php echo $this->res('icon/default/icon.css') ?>" rel="stylesheet">
    <?php echo $this->loadRes('header') ?>

    <script src="<?php echo $this->res('libs/jquery/1.11.1/jquery.min.js') ?>"></script>
    <script src="<?php echo $this->res('libs/bootstrap/3.3.6/js/bootstrap.min.js') ?>"></script>
    <script src="<?php echo $this->res('libs/particleground/1.1.0/jquery.particleground.min.js') ?>"></script>

    <!--[if lt IE 9]>
    <script src="<?php echo $this->res('libs/html5shiv/3.7.2/html5shiv.min.js') ?>"></script>
    <script src="<?php echo $this->res('libs/respond.js/1.4.2/respond.min.js') ?>"></script>
    <![endif]-->
</head>
<body>
<div id="particles">
    <div class="intro">
        <div class="container">
            <div class="row" style="margin:10px 0;">
                <div class="col-md-4 col-centered tac">
                    <img src="<?php echo $this->res('images/topics/logo.png') ?>" alt="logo"/>
                </div>

                <div class="col-md-12 col-centered tac" style="margin-top:20px;">
                    <h4><?php echo $this->siteConfig->get('introduce') ?></h4>
                </div>
            </div>
        </div>
        <?php
        if (!empty($alertMessage)) {
            echo $this->wrap('div', array('class' => 'container', 'style' => 'margin-top:30px;'))
                ->wrap('div', array('class' => 'row'))->wrap('div', array('class' => 'col-md-4 col-sm-8 col-centered'))
                ->html($alertMessage);
        }

        echo empty($content) ? "" : $content
        ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#particles').particleground({
            dotColor: 'rgba(52, 152, 219, 0.36)',
            lineColor: 'rgba(52, 152, 219, 0.86)',
            density: 130000,
            proximity: 500,
            lineWidth: 0.2
        });
    });
</script>
</body>
</html>
