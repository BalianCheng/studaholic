<?php
$smtp = &$data['smtp'];
$encrypt = &$data['encrypt'];
$rewrite = $this->e($data, 'rewrite') == 'on' ? 1 : 0;
$invite = $this->e($data, 'invite') == 'on' ? 1 : 0;

printf("<?php /* 最后生成日期: %s */ %s", date('Y-m-d H:i:s'), PHP_EOL);
?>
return array(
    'site_name' => '<?php echo $this->e($data, 'site_name', 'studaholic') ?>',
    'site_homepage' => '<?php echo $this->e($data, 'site_homepage') ?>',
    'title' => '<?php echo $this->e($data, 'title', 'studaholic') ?>',
    'keywords' => '<?php echo $this->e($data, 'keywords', 'studaholic') ?>',
    'description' => '<?php echo $this->e($data, 'description', 'studaholic课程交换平台提供课程交换，课程评价与交流服务。') ?>',

    //网站一句话介绍
    'introduce' => '<?php echo $this->e($data, 'introduce', 'studaholic课程交换平台') ?>',

    //发现频道模式
    'mode' => <?php echo $this->e($data, 'mode', 0) ?>,

    //服务器是否支持rewrite
    'rewrite' => <?php echo $rewrite ?>,

    //是否开启邀请注册
    'invite' => <?php echo $invite ?>,

    //加密相关
    'encrypt' => array(
        'uri' => '<?php echo $this->ee($encrypt, 'uri', \Cross\Core\Helper::random(16)) ?>',
        'auth' => '<?php echo $this->ee($encrypt, 'auth', \Cross\Core\Helper::random(16)) ?>'
    ),

    //默认模板文件夹
    'tpl_dir' => '<?php echo $this->e($data, 'tpl_dir', 'default') ?>',

    //邮件服务器
    'smtp' => array(
        'smtp_host' => '<?php echo $this->e($smtp, 'smtp_host', '') ?>',
        'smtp_port' => <?php echo $this->e($smtp, 'smtp_port', 465) ?>,
        'username' => '<?php echo $this->e($smtp, 'username', '') ?>',
        'password' => '<?php echo $this->e($smtp, 'password', '') ?>',
    )
);
