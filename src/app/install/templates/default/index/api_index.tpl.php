<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * index.php
 */
require __DIR__ . '/../src/crossboot.php';
$app = Cross\Core\Delegate::loadApp('forum', array(
    'sys' => array(
        'display' => 'JSON'
    )
));
try {
    $app->run();
} catch (Exception $e) {
    //记录日志等操作
    $app->get('error:exception', array('exception' => $e));
}
