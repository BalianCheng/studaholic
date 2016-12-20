<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Settings.php
 */

namespace app\admin\controllers;

use app\forum\modules\invite\InviteModule;
use app\forum\modules\common\SeoModule;
use Cross\Core\Helper;

/**
 * 设置
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Settings
 * @package app\admin\controllers
 */
class Settings extends Forum
{
    function index()
    {
        if ($this->is_post()) {
            $configFile = $this->getConfigFileAbsolutePath('site');
            $configFileContent = $this->view->obRenderTpl('config/site_config', $this->addslashes($_POST));

            $ret = file_put_contents($configFile, $configFileContent, LOCK_EX);
            if ($ret === false) {
                $this->data['status'] = 100200;
            } else {
                $this->to('settings');
            }
        }

        $this->data['config'] = $this->initGetSiteConfig();
        $this->display($this->data);
    }

    /**
     * seo
     *
     * @cp_params category
     */
    function seo()
    {
        $SM = new SeoModule();
        $seoConfig = $SM->getSeoConfig();

        $current = array();
        $controllerNameList = array();
        $category = $this->params['category'];
        foreach ($seoConfig as $config) {
            if (empty($category)) {
                $category = $config['controller'];
            }

            if ($category == $config['controller']) {
                $current = $config;
            }

            $controllerNameList[$config['controller']] = $config['name'];
        }

        if ($this->is_post()) {
            $id = &$_POST['id'];
            if ($id) {
                unset($_POST['id']);
                $SM->updateSeoConfig($id, $_POST);
            }

            $this->to('settings:seo');
        }

        $this->data['current'] = $current;
        $this->data['category'] = $category;
        $this->data['all_config'] = $seoConfig;
        $this->data['controller_list'] = $controllerNameList;
        $this->display($this->data);
    }

    /**
     * 第三方登录配置
     */
    function OAuth()
    {
        $configFile = '';
        $this->data['oauth'] = $this->readConfig('oauth', $configFile);
        if ($this->is_post()) {
            $configFileContent = $this->view->obRenderTpl('config/oauth_config', $this->addslashes($_POST['oauth']));
            $ret = file_put_contents($configFile, $configFileContent, LOCK_EX);
            if ($ret === false) {
                $this->data['status'] = 100200;
            } else {
                $this->to('settings:OAuth');
            }
        }

        $this->display($this->data);
    }

    /**
     * 邀请码管理
     */
    function inviteCode()
    {
        $IM = new InviteModule();
        $inviteCodeList = $IM->getAllInviteCode();

        if ($this->is_post()) {
            foreach ($_POST as $id => $config) {
                $data['comments'] = htmlentities(strip_tags($config['comments']), ENT_COMPAT, 'utf-8');
                if (isset($config['status']) && strcasecmp($config['status'], 'on') == 0) {
                    $data['status'] = 1;
                } else {
                    $data['status'] = 0;
                }
                $IM->updateInviteInfo($id, $data);
            }

            $this->to('settings:inviteCode');
        }

        $this->data['inviteCodeList'] = $inviteCodeList;
        $this->display($this->data);
    }

    /**
     * 添加邀请码
     */
    function addInviteCode()
    {
        if (!$this->is_post()) {
            $this->to('settings:inviteCode');
        }

        if (!empty($_POST['invite_code'])) {
            $invite_code = &$_POST['invite_code'];
            $IM = new InviteModule();
            $ret = $IM->checkInviteCode($invite_code);
            if ($ret['status'] == 1) {
                $this->data['status'] = 0;
                $this->data['message'] = '该邀请码已存在~';
            } else {
                $ret = $IM->createInviteCode($invite_code, $_POST['comments']);
                if ($ret) {
                    $this->data['status'] = 1;
                } else {
                    $this->data['status'] = 0;
                    $this->data['message'] = '添加邀请码失败, 请联系管理员';
                }
            }
            $this->display($this->data, 'JSON');
        }
    }

    /**
     * 删除邀请码
     *
     * @cp_params id
     */
    function delInviteCode()
    {
        $id = (int)$this->params['id'];
        $IM = new InviteModule();
        $IM->deleteInviteCode($id);
        $this->to('settings:inviteCode');
    }

    /**
     * 邀请码状态切换
     *
     * @cp_params id
     */
    function changeInviteCodeStatus()
    {
        $id = (int)$this->params['id'];
        $IM = new InviteModule();
        $inviteInfo = $IM->getInviteInfo($id);
        if ($inviteInfo) {
            $status = $inviteInfo['status'];
            if ($status == 1) {
                $data = array('status' => 0);
            } else {
                $data = array('status' => 1);
            }
            $IM->updateInviteInfo($id, $data);
        }
        $this->to('settings:inviteCode');
    }

    /**
     * 更新SEO缓存
     */
    function updateSeoConfig()
    {
        $SM = new SeoModule();
        $seoConfig = $SM->getSeoConfig();

        $this->data['config'] = $this->addslashes($seoConfig);
        $this->display($this->data);
        $this->to('settings:seo');
    }

    /**
     * 处理需转义字符
     *
     * @param array $data
     * @return array
     */
    private function addslashes(array &$data)
    {
        foreach ($data as &$p) {
            if (is_array($p)) {
                $p = array_map('addslashes', $p);
            } else {
                $p = addslashes($p);
            }
        }

        return $data;
    }

    /**
     * 获取网站配置
     *
     * @return mixed
     */
    private function initGetSiteConfig()
    {
        $config = $this->siteConfig;
        if (empty($config['site_homepage'])) {
            $homepage = $this->request->getHostInfo() . trim(dirname(dirname($_SERVER['PHP_SELF'])), '\\');
            $config['site_homepage'] = rtrim($homepage, '/') . '/';
        }

        if (empty($config['encrypt']['uri'])) {
            $config['encrypt']['uri'] = Helper::random(16);
        }

        if (empty($config['encrypt']['auth'])) {
            $config['encrypt']['auth'] = Helper::random(16);
        }

        return $config;
    }
}
