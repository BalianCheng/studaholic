<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * SettingsView.php
 */

namespace app\admin\views;

/**
 * @Auth: cmz <393418737@qq.com>
 * Class SettingsView
 * @package app\admin\views
 */
class SettingsView extends ForumView
{
    /**
     * 网站设置
     *
     * @param $data
     */
    function index($data)
    {
        $this->renderTpl('settings/index', $data);
    }

    /**
     * 搜索引擎优化
     *
     * @param $data
     */
    function seo($data)
    {
        $this->renderTpl('settings/seo', $data);
    }

    /**
     * 第三方登录配置
     *
     * @param array $data
     */
    function OAuth($data = array())
    {
        $data['oauth_config_name'] = array(
            'qq' => '腾讯QQ',
            'weibo' => '新浪微博',
            'weixin' => '微信',
        );

        $this->renderTpl('settings/oauth', $data);
    }

    /**
     * 所有邀请码
     *
     * @param $data
     */
    function inviteCode($data = array())
    {
        $this->renderTpl('settings/invite_code', $data);
    }

    /**
     * 刷新seo缓存配置文件
     *
     * @param array $data
     */
    function updateSeoConfig($data)
    {
        $content = $this->obRenderTpl('settings/seo_config', $data);
        $cache_file = $this->getFilePath('config/seo.config.php');

        file_put_contents($cache_file, $content, LOCK_EX);
    }
}
