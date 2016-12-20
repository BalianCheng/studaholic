# 安装日期: <?php echo date('Y-m-d H:i:s', TIME) ?> #

//mysql主机
$mysql_link = array(
    'host' => '<?php echo $this->e($data, 'db_host', '127.0.0.1') ?>',
    'port' => <?php echo $this->e($data, 'db_port', 3306) ?>,
    'user' => '<?php echo $this->e($data, 'db_user') ?>',
    'pass' => '<?php echo $this->e($data, 'db_pass') ?>',
    'prefix' => '<?php echo $this->e($data, 'db_prefix') ?>',
    'charset' => '<?php echo $this->e($data, 'db_charset') ?>',
);

//redis主机
$redis_link = array(
    'host' => '<?php echo $this->e($data, 'redis_host', '127.0.0.1') ?>',
    'port' => <?php echo $this->e($data, 'redis_port', 6379) ?>,
    'pass' => '<?php echo $this->e($data, 'redis_host') ?>',
    'timeout' => '<?php echo $this->e($data, 'redis_timeout', '2.5') ?>'
);

//默认数据库
$db = $mysql_link;
$db['name'] = '<?php echo $this->e($data, 'db_name') ?>';

return array(
    'mysql' => array(
        'db' => $db,
    )
);
