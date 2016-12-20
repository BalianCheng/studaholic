<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Web.php
 */

namespace app\install\controllers;


use Cross\MVC\Controller;

abstract class Install extends Controller
{
    /**
     * @var array
     */
    protected $data = array('status' => 0, 'step' => '开始安装');

    /**
     * @return mixed
     */
    abstract function index();
}
