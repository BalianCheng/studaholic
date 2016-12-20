<?php
/**
 * @Author:       cmz <393418737@qq.com>
 */
session_start();
require __DIR__ . '/../src/crossboot.php';
$admin =  Cross\Core\Delegate::loadApp('admin');

//登录成功后$_SESSION['u']会被赋值
//如果$_SESSION['u']为空,访问任何页面都只会输出登录界面
if (empty($_SESSION['u'])) {
    $admin->get('Main:login');
} else {
    $admin->run();
}
