<?php printf("<?php /* 最后生成日期: %s */ %s", date('Y-m-d H:i:s'), PHP_EOL); ?>
return array(
    'site_name' => 'studaholic',
    'site_homepage' => '<?php echo $this->e($data, 'site_homepage') ?>',
    'title' => 'studaholic',
    'keywords' => 'studaholic',
    'description' => 'studaholic课程交换平台提供课程交换，课程评价与交流服务。',

    //网站一句话介绍
    'introduce' => 'studaholic课程交换平台',

    //发现频道模式
    'mode' => <?php echo $this->e($data, 'mode', 0) ?>,

    //是否开启邀请注册
    'invite' => 0,

    //加密相关
    'encrypt' => array(
        'uri' => '<?php echo \Cross\Core\Helper::random(16) ?>',
        'auth' => '<?php echo \Cross\Core\Helper::random(16) ?>'
    ),

    //默认模板文件夹
    'tpl_dir' => '<?php echo $this->e($data, 'tpl_dir', 'default') ?>',

    //邮件服务器
    'smtp' => array(
        'smtp_host' => '',
        'smtp_port' => 465,
        'username' => '',
        'password' => '',
    )
);
