<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Test.php
 */

namespace app\install\controllers;


use Cross\Core\Loader;
use Cross\DB\DBFactory;
use Exception;

/**
 * 处理安装过程中的各种测试
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Test
 * @package app\install\controllers
 */
class Test extends Install
{
    /**
     * @return mixed
     */
    function index()
    {
        $this->to();
    }

    /**
     * 数据库配置测试
     *
     * @throws \Cross\Exception\CoreException
     */
    function db()
    {
        if (!version_compare(PHP_VERSION, '5.3.6', '>=')) {
            $this->display($this->result(0, 'PHP版本不能低于5.3.6(当前版本' . PHP_VERSION . ')'), 'JSON');
        } else {
            $empty_notice = array(
                'db_host' => '数据库主机不能为空',
                'db_port' => '数据库端口不能为空',
                'db_user' => '数据库用户名不能为空',
                'db_pass' => '数据库密码不能为空',
                'db_name' => '数据库名称不能为空',
            );

            $db_config = array();
            foreach ($empty_notice as $key => $notice) {
                if (!isset($_POST[$key]) || empty($_POST[$key])) {
                    $this->display($this->result(0, $notice), 'JSON');
                    return;
                }

                $db_config[$key] = htmlentities($_POST[$key], ENT_COMPAT, 'utf-8');
            }

            try {
                $db = DBFactory::make('mysql', array(
                    'host' => $db_config['db_host'], 'port' => $db_config['db_port'],
                    'user' => $db_config['db_user'], 'pass' => $db_config['db_pass'],
                    'name' => $db_config['db_name'],
                ));

                $versionInfo = $db->fetchOne('select version() version');
                $version = $versionInfo['version'];
                if ($version) {
                    list($version,) = explode('-', $version);
                    if (!version_compare($version, '5.0.0', '>=')) {
                        $this->display($this->result(0, "MySQL版本不能低于5.0.0(当前版本{$version})"), 'JSON');
                    } else {
                        $this->display($this->result(1, '测试成功!, 请点击开始按钮进行安装'), 'JSON');
                    }
                } else {
                    $this->display($this->result(0, '测试失败,不能获取MySQL版本'), 'JSON');
                }
            } catch (Exception $e) {
                $this->display($this->result(0, '连接失败, ' . $e->getMessage()), 'JSON');
            }
        }
    }

    /**
     * 检查数据库配置文件
     */
    function dbFile()
    {
        $db_file = $this->getFilePath('config::db.config.php');
        if (!file_exists($db_file)) {
            $this->display($this->result(0, '保存数据库配置文件失败, 请检查config目录是否可写'), 'JSON');
        } else {
            $this->display($this->result(1, '保存数据库配置文件成功!'), 'JSON');
        }
    }
}
