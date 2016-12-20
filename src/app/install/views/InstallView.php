<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * WebView.php
 */

namespace app\install\views;


use Cross\MVC\View;

/**
 * @Auth: cmz <393418737@qq.com>
 * Class WebView
 * @package app\web\views
 */
class InstallView extends View
{
    function step()
    {
        return $this->data['step'];
    }

    function style()
    {
        if (isset($this->data['backgroundColor'])) {
            return 'background-color:' . $this->data['backgroundColor'];
        }

        return '';
    }

    function icon() {
        if(!empty($this->data['icon'])) {
            return $this->img($this->res($this->data['icon']));
        }

        return $this->img($this->res('images/default_icon.png'));
    }
}
