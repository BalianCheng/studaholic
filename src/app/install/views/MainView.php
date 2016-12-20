<?php
/**
 * @Author:       cmz <393418737@qq.com>
 */
namespace app\install\views;

use Cross\Core\Helper;
use Cross\Core\Loader;

/**
 * @Auth: cmz <393418737@qq.com>
 * Class MainView
 * @package app\web\views
 */
class MainView extends InstallView
{
    /**
     * 默认视图控制器
     *
     * @param array $data
     */
    function index($data = array())
    {
        $this->renderTpl('main/index', $data['db']);
    }

    /**
     * 保存配置
     *
     * @param array $data
     */
    function saveConfig($data = array())
    {
        $content = $this->obRenderTpl('config/db', $data['db_config']);
        $db_config_file = $this->getFilePath('config::db.config.php');
        Helper::mkfile($db_config_file);
        file_put_contents($db_config_file, "<?php \r\n" . $content);
        $this->renderTpl('main/save_config');
    }

    /**
     * 配置管理员帐号
     *
     * @param array $data
     */
    function account($data = array())
    {
        $this->renderTpl('main/account', $data);
    }

    /**
     * 导入SQL结构
     *
     * @param array $data
     */
    function import($data = array())
    {
        $this->renderTpl('main/import_sql');
    }

    /**
     * 安装锁定页面
     *
     * @param array $data
     */
    function installLock($data = array())
    {
        $this->renderTpl('main/install_lock', $data);
    }

    /**
     * 输出错误信息
     *
     * @param array $data
     */
    function errorInfo($data=array())
    {
        $this->renderTpl('main/error_info', $data);
    }

    /**
     * 初始化数据
     *
     * @param array $data
     */
    function initData($data = array())
    {
        $this->renderTpl('main/init_data');
    }

    /**
     * 安装成功
     *
     * @param array $data
     */
    function end($data = array())
    {
        $this->renderTpl('main/end', $data);
    }
}
