<?php
namespace app\forum\modules\common;

use Cross\Core\Helper;
use Cross\Core\Loader;
use Cross\MVC\Module;
use Exception;

/**
 * 日志
 *
 * @Auth: cmz <393418737@qq.com>
 * LogModule.php
 */
class LogModule extends Module
{
    /**
     * 异常日志
     *
     * @param Exception $exception
     * @return string
     */
    static function exception(Exception $exception)
    {
        //今天的第几秒
        $log_id = str_pad(TIME - strtotime(date('Y-m-d')), 5, 0, STR_PAD_LEFT) . '.' . Helper::random(3, true);
        if ($exception instanceof Exception) {
            $log['msg'] = $exception->getMessage();
            $log['trace'] = explode("\n", $exception->getTraceAsString());
        } else {
            $log['msg'] = $exception;
        }

        $router = self::$app_delegate->getRouter();
        $log['controller'] = sprintf('%s:%s', $router->getController(), $router->getAction());
        $log['params'] = json_encode($router->getParams());

        $log['date'] = date('Y-m-d H:i:s');
        $log['client_ip'] = Helper::getIp();
        $log['request_uri'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $log['http_referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

        $exception_log_file = self::$app_delegate->getConfig()->get('path', 'cache') . 'exception' .
            DIRECTORY_SEPARATOR . date('Y-m/d') . '.txt';

        Helper::mkfile($exception_log_file);
        $log_content = self::contextToString($log_id, $log);
        error_log($log_content, 3, $exception_log_file);
        return $log_id;
    }

    /**
     * 整理异常文件内容
     *
     * @param string $log_id
     * @param string $context
     * @return string
     */
    protected static function contextToString($log_id, $context)
    {
        $content = print_r($context, true);
        $content = explode("\n", $content);
        $content = array_filter($content);

        array_pop($content);
        array_shift($content);
        array_shift($content);

        $export = sprintf("---------- %s ----------" . PHP_EOL, date('Y-m-d H:i:s'));
        foreach ($content as $key => $value) {
            $value = str_replace(array('(', ')'), array('', ''), $value);
            $export .= sprintf("[%s]%s", $log_id, trim($value) . PHP_EOL);
        }
        return $export;
    }
}
