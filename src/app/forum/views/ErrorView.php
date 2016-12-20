<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * ErrorView.php
 */

namespace app\forum\views;

/**
 * 错误视图控制器
 *
 * @Auth: cmz <393418737@qq.com>
 * Class ErrorView
 * @package app\forum\views
 */
class ErrorView extends ForumView
{
    function exception($data)
    {
        $this->set(array(
            'load_layer' => false,
        ));

        $content = sprintf('%s(%s)', $data['message'], $data['log_id']);
        include $this->getTplBasePath().'exception.layer.php';
    }
}
