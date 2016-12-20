<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Forum.php
 */

namespace app\admin\controllers;

use app\forum\modules\common\BaseModule;
use app\forum\modules\title\TitleModule;
use Cross\Core\Loader;

/**
 * 管理后台控制器基类
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Forum
 * @package app\admin\controllers
 */
class Forum extends Admin
{
    /**
     * @var array
     */
    protected $siteConfig;

    function __construct()
    {
        parent::__construct();
        $this->siteConfig = $this->readConfig('site');
        $this->view->setSiteConfig($this->siteConfig);
    }

    /**
     * 内容跳转
     *
     * @cp_params title_id
     */
    function jumpToContent()
    {
        $title_id = (int)$this->params['title_id'];
        if (!$title_id) {
            $this->to('content');
        }

        $TM = new TitleModule();
        $title_info = $TM->getTitleSimpleDetailInfo($title_id);
        if (!$title_info) {
            $this->to('content');
        }

        if(!isset(BaseModule::$typeMap[$title_info['type']])) {
            $this->to('content');
        }

        $typeName = BaseModule::$typeMap[$title_info['type']];
        $params_id = $title_info["{$typeName}_id"];

        $url = $this->view->appUrl($this->siteConfig['site_homepage'], 'forum', "content:{$typeName}", array('id' => $params_id));
        $this->redirect($url);
    }

    /**
     * 读取配置文件
     *
     * @param string $fileName
     * @param string $configFile
     * @return mixed
     */
    protected function readConfig($fileName, &$configFile = '')
    {
        $configFile = $this->getConfigFileAbsolutePath($fileName);
        $defaultConfigFile = $this->getConfigFileAbsolutePath("default.{$fileName}");
        if (!file_exists($configFile) && file_exists($defaultConfigFile)) {
            copy($defaultConfigFile, $configFile);
        }

        return $this->parseGetFile("config::{$fileName}.config.php");
    }

    /**
     * 配置文件绝对路径
     *
     * @param string $config
     * @return mixed
     */
    protected function getConfigFileAbsolutePath($config)
    {
        return $this->getFilePath("config::{$config}.config.php");
    }
}
