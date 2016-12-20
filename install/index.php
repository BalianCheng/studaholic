<?php
/**
 * @Author:       cmz <393418737@qq.com>
 */
require __DIR__ . '/../src/crossboot.php';
$app = Cross\Core\Delegate::loadApp('install');
if (file_exists(__DIR__ . '/install.lock')) {
    $app->get('main:installLock');
} else {
    try {
        $app->run();
    } catch (Exception $e) {
        $app->get('main:errorInfo', array('message' => $e->getMessage()));
    }
}
