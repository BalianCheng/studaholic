<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Error.php
 */
namespace app\forum\controllers;

use app\forum\modules\common\LogModule;

/**
 * 错误处理
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Error
 * @package app\web\controllers
 */
class Error extends Forum
{
    function index()
    {

    }

    /**
     * 异常处理
     *
     * @return mixed
     */
    function exception()
    {
        $exception = $this->params['exception'];
        $log_id = LogModule::exception($exception);

        $this->data['status'] = 200001;
        $this->data['log_id'] = $log_id;
        $this->display($this->data);
    }
}
