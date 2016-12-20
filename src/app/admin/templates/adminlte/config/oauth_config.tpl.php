<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * oauth.config.php
 */
printf('<?php //最后更新 %s' . PHP_EOL, date('Y-m-d H:i:s'));

?>
return array(
<?php foreach($data as $k => $v) : ?>
    '<?php echo $k ?>' => array( 'app_id' => '<?php echo $v['app_id'] ?>', 'app_key' => '<?php echo $v['app_key'] ?>' ),
<?php endforeach; ?>
);
