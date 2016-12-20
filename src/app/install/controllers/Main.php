<?php
/**
 * @Author:       cmz <393418737@qq.com>
 */
namespace app\install\controllers;

use app\admin\modules\admin\AdminModule;
use Cross\Core\Helper;
use Cross\DB\DBFactory;

/**
 * 安装过程控制
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Main
 * @package app\install\controllers
 */
class Main extends Install
{
    /**
     * 数据库默认配置
     *
     * @var array
     */
    protected $default_db_config = array(
        'db_host' => '127.0.0.1',
        'db_port' => 3306,
        'db_user' => '',
        'db_pass' => '',
        'db_name' => '',
        'db_charset' => 'utf8',
        'db_prefix' => 'bbs_',
    );

    /**
     * 安装首页
     */
    function index()
    {
        $this->data['db'] = $this->default_db_config;
        $this->display($this->data);
    }

    /**
     * 保存数据库配置文件
     */
    function saveConfig()
    {
        if ($this->is_post()) {
            $db_config = $this->default_db_config;
            foreach ($db_config as $key => $value) {
                if (isset($_POST[$key]) && !empty($_POST[$key])) {
                    $db_config[$key] = addslashes(trim($_POST[$key]));
                }
            }

            $this->data['icon'] = 'images/config.png';
            $this->data['step'] = '保存数据库配置';
            $this->data['db_config'] = $db_config;
            $this->display($this->data);
        } else {
            $this->to();
        }
    }

    /**
     * 导入SQL文件
     */
    function import()
    {
        $this->data['icon'] = 'images/import.png';
        $this->data['step'] = '安装数据结构';
        $this->data['backgroundColor'] = '#2e8b57';
        $this->display($this->data);
    }

    /**
     * 初始化数据（管理菜单等）
     */
    function initData()
    {
        $this->data['icon'] = 'images/init_data.png';
        $this->data['step'] = '初始化数据';
        $this->display($this->data);
    }

    /**
     * 配置管理员帐号
     */
    function account()
    {
        $this->data['icon'] = 'images/admin.png';
        $this->data['step'] = '创建超级管理员帐号';
        $this->display($this->data);
    }

    /**
     * 安装完成
     */
    function end()
    {
        $base_url = $this->request->getBaseUrl(true);
        $home_url = substr($base_url, 0, strlen($base_url) - strlen('install'));
        $admin_url = $home_url . 'admin/';

        $this->data['home_url'] = $home_url;
        $this->data['admin_url'] = $admin_url;

        $this->data['icon'] = 'images/success.png';
        $this->data['backgroundColor'] = '#00a65a';
        $this->data['step'] = '完成';
        $this->data['end'] = '完成';
        $this->display($this->data);
    }

    /**
     * 安装完成
     */
    function addAccount()
    {
        $admin = new AdminModule();
        $admin_info = $admin->getAdminInfo(array('name' => $_POST['name']));
        if ($admin_info) {
            $this->display($this->result(0, '超级管理员已存在'), 'JSON');
        } else {
            $_POST['rid'] = 0;
            $admin_id = $admin->addAdmin($_POST);
            if ($admin_id) {
                $this->display($this->result(1, '创建管理员成功'), 'JSON');
            } else {
                $this->display($this->result(0, '创建管理员失败, 请联系售后'), 'JSON');
            }
        }
    }

    /**
     * 创建安装锁定文件
     */
    function lock()
    {
        if ($this->is_ajax_request()) {
            $web_index = $this->view->getTplPath() . 'index/web_index.tpl.php';
            $api_index = $this->view->getTplPath() . 'index/api_index.tpl.php';
            $admin_index = $this->view->getTplPath() . 'index/admin_index.tpl.php';

            //入口文件目录
            $base_dir = dirname(dirname($_SERVER['SCRIPT_FILENAME'])) . DIRECTORY_SEPARATOR;

            //创建入口文件
            $web_status = copy($web_index, $base_dir . 'index.php');
            $api_status = copy($api_index, $base_dir . 'api/index.php');
            $admin_status = copy($admin_index, $base_dir . 'admin/index.php');

            if (Helper::mkfile('install.lock') && $web_status && $api_status && $admin_status) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            $this->to();
        }
    }

    /**
     * 安装锁定提示
     */
    function installLock()
    {
        $base_url = $this->request->getBaseUrl(true);
        $home_url = substr($base_url, 0, strlen($base_url) - strlen('install'));
        $admin_url = $home_url . 'admin/';

        $this->data['home_url'] = $home_url;
        $this->data['admin_url'] = $admin_url;

        $this->data['icon'] = 'images/lock.png';
        $this->data['backgroundColor'] = '#dd4b39';
        $this->data['step'] = '安装程序已锁定';
        $this->display($this->data);
    }

    /**
     * @cp_params message=程序出现问题了，请联系管理员。
     * @param null $message
     */
    function errorInfo($message = null)
    {
        if (null === $message) {
            $message = &$this->params['message'];
        }

        $this->data['icon'] = 'images/error.png';
        $this->data['backgroundColor'] = '#dd4b39';
        $this->data['message'] = $message;
        $this->data['step'] = '出错了';
        $this->display($this->data, 'errorInfo');
    }

    /**
     * 执行导入操作
     *
     * @throws \Cross\Exception\CoreException
     * @cp_params s=0
     */
    function doImport()
    {
        set_time_limit(0);
        $db = $this->parseGetFile('config::db.config.php');
        $prefix = &$db['mysql']['db']['prefix'];

        $sql = $this->view->getTplPath() . 'config/base.sql';
        $sql = file_get_contents($sql);
        $sql = str_replace('PREFIX@', $prefix, $sql);
        $sql_segments = preg_split("/\s*^\s*$/sm", $sql);
        $sql_segments = array_filter($sql_segments);
        $sql_segments_count = count($sql_segments);
        sort($sql_segments);

        $s = min($sql_segments_count, max((int)$this->params['s'], 0));
        if (isset($sql_segments[$s])) {
            $link = DBFactory::make('mysql', $db['mysql']['db']);
            $link->execute($sql_segments[$s]);

            preg_match('/NOT EXISTS `(.*?)`/i', $sql_segments[$s], $match);
            $next = $this->view->url("main:doImport", array('s' => $s + 1));
            $parent = 0;
            $message = sprintf("正在创建表(%s/%s)：%s", $s + 1, $sql_segments_count, $match[1]);
        } else {
            $next = $this->view->url('main:initData');
            $parent = 1;
            $message = "创建完成";
        }
        ?>
        <script>
            var t = parent.window.document.getElementById('process-notice');
            t.scrollTop = t.scrollHeight;

            parent.window.document.getElementById('process-notice').innerHTML += "<?php echo $message ?>\r\n";
            <?php if($parent && $next) : ?>
            parent.window.location.href = '<?php echo $next ?>';
            <?php elseif($next) : ?>
            window.location.href = '<?php echo $next ?>';
            <?php endif ?>
        </script>
        <?php
    }

    /**
     * 初始化数据
     *
     * @cp_params s=0
     * @throws \Cross\Exception\CoreException
     */
    function doInitData()
    {
        set_time_limit(0);
        $step = min(1, max(0, (int)$this->params['s']));
        switch ($step) {
            case 0:
                $configPath = $this->config->get('path', 'config');
                $configFile = $configPath . 'site.config.php';
                $homepage = $this->request->getHostInfo() . trim(dirname(dirname($_SERVER['PHP_SELF'])), '\\');
                $config['site_homepage'] = rtrim($homepage, '/') . '/';
                $content = $this->view->obRenderTpl('config/site_config', $config);
                if (false === file_put_contents($configFile, $content, LOCK_EX)) {
                    $message = '初始化站点配置失败';
                    $parent = false;
                    $next = false;
                } else {
                    $message = '初始化站点配置成功';
                    $parent = false;
                    $next = $this->view->url('main:doInitData', array('s' => 1));
                }
                break;

            case 1:
                $db = $this->parseGetFile('config::db.config.php');
                $prefix = &$db['mysql']['db']['prefix'];

                $sql = $this->view->getTplPath() . 'config/data.sql';
                $sql = file_get_contents($sql);
                $sql = str_replace('PREFIX@', $prefix, $sql);

                $link = DBFactory::make('mysql', $db['mysql']['db']);
                $link->execute($sql);
                $message = '初始化数据成功';
                $parent = true;
                $next = $this->view->url('main:account');
                break;

            default:
                $message = '非法操作';
                $parent = false;
                $next = false;
        }

        ?>
        <script>
            parent.window.document.getElementById("process-notice").innerHTML += "<?php echo $message ?>\r\n";
            <?php if($parent && $next) :?>
            parent.window.location.href = '<?php echo $next ?>';
            <?php elseif($next) : ?>
            window.location.href = '<?php echo $next ?>';
            <?php endif ?>
        </script>
        <?php
    }

}
