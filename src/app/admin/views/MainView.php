<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * loginView.php
 */
namespace app\admin\views;

class MainView extends AdminView
{
    function __construct() {
        parent::__construct();

        //设置布局
        $this->set(array(
            'layer' =>  'login'
        ));
    }

    /**
     * 登录页面处理
     *
     * @param array $data
     */
    function login($data = array())
    {
        $this->renderTpl("main/index", $data);
    }
}
