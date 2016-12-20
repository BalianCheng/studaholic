<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * WebView.php
 */

namespace app\forum\views;

use app\forum\modules\account\PlatformModule;
use app\forum\modules\common\BaseModule;
use Cross\Exception\CoreException;
use Cross\Core\CrossArray;
use Cross\Core\Config;
use Cross\Core\Helper;
use Cross\MVC\View;

/**
 * @Auth: cmz <393418737@qq.com>
 * Class WebView
 * @package app\web\views
 */
class ForumView extends View
{
    /**
     * 模型名称配置
     *
     * @var array
     */
    protected $modeName;

    /**
     * seo配置
     *
     * @var CrossArray
     */
    protected $seoConfig;

    /**
     * 全局信息配置
     *
     * @var CrossArray
     */
    protected $siteConfig;

    /**
     * 排序菜单
     *
     * @var array
     */
    protected $orderMenuMembers = array('time' => '最新', 'interact' => '热度');

    /**
     * 内容导航菜单
     *
     * @var array
     */
    private $contentNavMenu = array();

    /**
     * 推荐话题
     *
     * @var array
     */
    private $recommendTopic = array();

    /**
     * 配置
     *
     * @param Config $config
     */
    function setSiteConfig(Config $config)
    {
        $this->siteConfig = $config;
    }

    /**
     * 设置模型名称
     *
     * @param array $modeName
     */
    function setModeName(array $modeName)
    {
        $this->modeName = $modeName;
    }

    /**
     * seo配置
     *
     * @param Config $config
     */
    function setSeoConfig(Config $config)
    {
        $this->seoConfig = $config;
    }

    /**
     * 设置推荐话题
     *
     * @param $topic
     */
    function setRecommendTopic(array $topic)
    {
        $this->recommendTopic = $topic;
    }

    /**
     * 获取推荐话题
     *
     * @return array
     */
    function getRecommendTopic()
    {
        return $this->recommendTopic;
    }

    /**
     * 设置内容导航菜单数据
     *
     * @param array $data
     */
    function setContentNavMenu(array $data)
    {
        $this->contentNavMenu = $data;
    }

    /**
     * 获取内容导航菜单数据
     *
     * @return array
     */
    function getContentNavMenu()
    {
        return $this->contentNavMenu;
    }

    /**
     * 输出seo相关内容
     *
     * @param string $type
     * @return array|bool|string
     */
    function getSeoInfo($type)
    {
        $content = '';
        $globalContent = $this->siteConfig->get($type);
        $seoKey = sprintf('%s.%s', lcfirst($this->controller), $this->action);
        $seoConfig = $this->seoConfig->get($seoKey);
        if (isset($seoConfig[$type])) {
            $content = $seoConfig[$type];
            $isMatch = preg_match_all('#{{(.*?)}}#i', $content, $matches);
            if ($isMatch) {
                foreach ($matches[1] as &$m) {
                    $data = array();
                    $ma = explode('.', $m);
                    while (null !== $key = array_shift($ma)) {
                        if (empty($data) && isset($this->data[$key])) {
                            $data = $this->data[$key];
                        } elseif (isset($data[$key])) {
                            $data = $data[$key];
                        } else {
                            $data = array();
                        }
                    }

                    if (empty($data)) {
                        $m = '';
                    } elseif (is_array($data)) {
                        $m = current($data);
                    } else {
                        $m = $data;
                    }
                }
                $content = str_replace($matches[0], $matches[1], $content);
            }

        } elseif (isset($this->set[$type])) {
            $content = $this->set[$type];
        }

        if ($content) {
            return "{$content} - " . $globalContent;
        } else {
            return $globalContent;
        }
    }

    /**
     * 输出推荐话题
     */
    function recommendNavTopic()
    {
        $recommendTopic = $this->getRecommendTopic();
        foreach ($recommendTopic as $topic) {
            echo $this->a($topic['topic_name'], $this->url('topics:detail', array('topic_url' => $topic['topic_url'])));
        }
    }

    /**
     * 类型选择菜单
     *
     * @param array $content_type_status
     * @param $topic_url
     * @param string $current
     */
    function contentTypeMenu($content_type_status, $topic_url, $current)
    {
        foreach ($this->modeName as $type => $name) {
            if (empty($content_type_status[$type])) {
                continue;
            }

            if ($current == $type) {
                $wrap['class'] = 'active';
                $attr['class'] = 'content-type';
            } else {
                $wrap = array();
                $attr['class'] = 'content-type';
            }

            $url = $this->url('topics:detail', array('topic_url' => $topic_url, 'type' => $type));
            echo $this->wrap('li', $wrap)->a($name, $url, $attr);
        }
    }

    /**
     * @see makeOrderMenu
     *
     * @param string $controller
     * @param string $params
     * @param string $current
     */
    function orderMenu($controller, $params, $current)
    {
        echo $this->makeOrderMenu($this->orderMenuMembers, $controller, $params, $current);
    }

    /**
     * 生成排序菜单
     *
     * @param array $members
     * @param string $controller
     * @param array $params
     * @param string $current
     * @return string
     */
    function makeOrderMenu(array $members, $controller, $params, $current)
    {
        $result = array();
        foreach ($members as $order => $name) {
            $attr = array();
            $params['order'] = $order;
            if ($order == $current) {
                $attr = array('class' => 'active');
            }

            $result[] = $this->a($name, $this->url($controller, $params), $attr);
        }

        return implode('|', $result);
    }

    /**
     * 内容导航菜单
     *
     * @param string $wrap
     * @param bool $xs_model
     * @param array $wrapDefaultAttr
     * @param array $aDefaultAttr
     */
    function contentNavMenu($wrap = 'li', $xs_model = false, array $wrapDefaultAttr = array(), array $aDefaultAttr = array())
    {
        $contentNavMenu = $this->getContentNavMenu();
        foreach ($contentNavMenu as $menu) {
            if ($menu['login_display']) {
                if (!$this->data['isLogin']) {
                    continue;
                }
            }

            if ($xs_model && $menu['xs'] != 1) {
                continue;
            }

            $wrapAttr = $wrapDefaultAttr;
            if (!empty($menu['current']) && strtolower($this->controller) == $menu['current']) {
                $aText = $menu['name'] . '<span class="sr-only">(current)</span>';
                $wrapAttr['class'] = 'active';
            } else {
                $aText = $menu['name'];
            }

            $aAttr = $aDefaultAttr;
            if (!empty($menu['target'])) {
                $aAttr['target'] = $menu['target'];
            }

            if ($menu['type'] == 1) {
                $link = empty($menu['link']) ? $this->url() : $this->url($menu['link']);
            } else {
                $link = $menu['link'];
            }

            echo $this->wrap($wrap, $wrapAttr)->a($aText, $link, $aAttr);
        }
    }

    /**
     * 输出内容所属话题
     *
     * @param $data
     * @param $type
     */
    function contentTopics($data, $type)
    {
        foreach ($data as $d) {
            $topic_url = $this->url('topics:detail', array('topic_url' => $d['topic_url'], 'type' => $type));
            echo $this->a($this->htmlTag('button', array('@content' => $d['topic_name'], 'class' => 'btn btn-sm btn-topic-list')), $topic_url);
        }
    }

    /**
     * 动态内容列表
     *
     * @param $data
     * @param string $type 动态模版类型
     * @param array $wrapAttr
     */
    function contentList(array $data, $type = 'content', $wrapAttr = array('class' => 'content-list'))
    {
        $this->contentListSection($data, "fragment/{$type}", $wrapAttr);
    }

    /**
     * 动态内容列表
     *
     * @param array $data
     * @param string $tpl_prefix
     * @param array $wrapAttr
     */
    function contentListSection(array $data, $tpl_prefix, $wrapAttr = array('class' => 'content-list'))
    {
        if (!empty($data)) {
            foreach ($data as $d) {
                switch ($d['type']) {
                    case BaseModule::TYPE_QUESTION:
                        echo $this->section("{$tpl_prefix}/question", $d, $wrapAttr);
                        break;

                    case BaseModule::TYPE_POSTS:
                        echo $this->section("{$tpl_prefix}/posts", $d, $wrapAttr);
                        break;

                    case BaseModule::TYPE_ARTICLE:
                        echo $this->section("{$tpl_prefix}/article", $d, $wrapAttr);
                        break;
                }
            }
        }
    }

    /**
     * 输出标题和链接
     *
     * @param array $data
     * @return mixed|string
     */
    function simpleTitleUrl(array $data)
    {
        switch ($data['type']) {
            case BaseModule::TYPE_QUESTION:
                $params = array('question_id' => $data['question_id']);
                $controller = 'content:question';
                break;

            case BaseModule::TYPE_POSTS:
                $params = array('posts_id' => $data['posts_id']);
                $controller = 'content:posts';
                break;

            case BaseModule::TYPE_ARTICLE:
                $params = array('article_id' => $data['article_id']);
                $controller = 'content:article';
                break;

            default:
                $params = array();
                $controller = 'explore:index';
                $data['title'] = '发现';
        }

        return $this->a($data['title'], $this->url($controller, $params));
    }

    /**
     * 输出用户头像
     *
     * @param string $img
     * @param string $size
     * @param string $class
     * @return string
     */
    function userAvatar($img, $size = '42px', $class = 'img-circle user-avatar')
    {
        $avatarAbsoluteUrl = $this->resAbsoluteUrl($img);
        $style = "width:{$size};height:{$size};";
        return $this->img($avatarAbsoluteUrl, array(
            'style' => $style, 'alt' => 'user avatar', 'class' => $class,
        ));
    }

    /**
     * 获取资源绝对路径
     *
     * @param string $url
     * @return string
     */
    function resAbsoluteUrl($url)
    {
        static $cache = array();
        if (!isset($cache[$url])) {
            $isAbs = false;
            $absoluteDefine = array('https://' => 8, 'http://' => 7, '//' => 2);
            foreach ($absoluteDefine as $scheme => $length) {
                if (strncasecmp($scheme, $url, $length) === 0) {
                    $isAbs = true;
                    break;
                }
            }

            if (!$isAbs) {
                $url = $this->res($url);
            }

            $cache[$url] = $url;
        }

        return $cache[$url];
    }

    /**
     * 用户主页昵称连接
     *
     * @param $account
     * @param $nickname
     * @param $introduce
     * @param bool $addIntroduce
     * @param array $element_tag
     * @return mixed|string
     */
    function userNickname($account, $nickname, $introduce, $addIntroduce = true, $element_tag = array())
    {
        if (empty($nickname)) {
            $nickname = '火星用户';
        }

        $ul = $this->a($nickname, $this->url('user:detail', array('account' => $account)), $element_tag);
        if (!empty($introduce) && $addIntroduce) {
            $ul .= ', ' . $introduce;
        }

        return $ul;
    }

    /**
     * 登录按钮
     *
     * @param string $back_url
     */
    function loginButton($back_url = '')
    {
        $data['back_url'] = $back_url;
        $platformConfig = $this->getPlatformConfig();
        if (!empty($platformConfig)) {
            $data['platform'] = $platformConfig;
            $this->renderTpl('user/enter/platform_login', $data);
        } else {
            $this->renderTpl('user/enter/normal_login', $data);
        }
    }

    /**
     * 注册按钮
     *
     * @param string $back_url
     */
    function registerButton($back_url = '')
    {
        $data['back_url'] = $back_url;
        $platformConfig = $this->getPlatformConfig();
        if (!empty($platformConfig)) {
            $data['platform'] = $platformConfig;
            $this->renderTpl('user/enter/platform_register', $data);
        } else {
            $this->renderTpl('user/enter/normal_register', $data);
        }
    }

    /**
     * 登录用户信息
     *
     * @param array $data
     * @return mixed|string
     */
    function loginUserNickname(array $data)
    {
        return $this->userNickname($data['account'], $data['nickname'], $data['introduce'], true);
    }

    /**
     * 输出话题标识
     *
     * @param string $image
     * @param string $size
     * @param string $class
     */
    function topicSigns($image, $size = '48px', $class = '')
    {
        $attr = array();
        if ($size) {
            $attr['style'] = "width:{$size};height:{$size};";
        }

        if ($class) {
            $attr['class'] = $class;
        }

        echo $this->img($this->resAbsoluteUrl($image), $attr);
    }

    /**
     * 输出内容中包含的图片
     *
     * @param array $images
     * @param string $type
     * @param string $image_link
     */
    function contentImages($images, $type, $image_link = '')
    {
        $style = '';
        if ($type == 'small') {
            $style = 'height:96px;width:96px;';
        } elseif ($type == 'small-xs') {
            $style = 'height:30px;width:30px;';
        }

        $loadImage = $this->res('images/load_images.gif');
        if (!empty($images)) {
            foreach ($images as $k => $d) {
                if ($k >= 5) {
                    break;
                }

                $img = $this->img($loadImage, array(
                    'style' => $style,
                    'class' => 'lazy',
                    'data-original' => $d['image_url'],
                ));

                echo $this->a($img, $image_link);
            }
        }
    }

    /**
     * 输出带模板的图片
     *
     * @param $image_list
     * @param string $type
     * @param string $content_link
     * @param int $image_max
     */
    function images($image_list, $type = 'small', $content_link = '', $image_max = 5)
    {
        $this->renderTpl("fragment/images/{$type}", array(
            'image_max' => $image_max,
            'image_list' => $image_list,
            'content_link' => $content_link,
        ));
    }

    /**
     * 友好时间
     *
     * @param string $t UNIX时间戳
     * @return string
     */
    function fTime($t)
    {
        return Helper::ftime($t);
    }

    /**
     * @see Helper::subStr
     *
     * @param $str
     * @param $length
     * @param $enc
     * @return string
     */
    function subStr($str, $length = 128, $enc = 'utf8')
    {
        return Helper::subStr($str, $length, $enc);
    }

    /**
     * 小分辨率下隐藏的友好时间显示
     *
     * @param string $t
     * @param string $txt
     * @param string $dom
     * @return string
     */
    function xsHideFTime($t, $txt = '', $dom = 'span')
    {
        return $this->htmlTag($dom, array('class' => 'hidden-xs', '@content' => sprintf("%s%s", $txt, $this->fTime($t))));
    }

    /**
     * 安全的输出截取过的HTML字符串
     *
     * @param $string
     * @param bool $html_decode
     * @return string
     */
    function safeHTMLString($string, $html_decode = true)
    {
        if ($html_decode) {
            return html_entity_decode(Helper::formatHTMLString($string));
        }
        return Helper::formatHTMLString($string);
    }

    /**
     * 友好数量
     *
     * @param string $count
     * @return string
     */
    function fCount($count)
    {
        if ($count >= 1000000) {
            $count = floor(sprintf('%f', $count / 1000000 * 10)) / 10 . 'm';
        } elseif ($count >= 1000) {
            $count = floor(sprintf('%.1f', $count / 1000 * 10)) / 10 . 'k';
        }

        return $count;
    }

    /**
     * 推荐用户分类标题
     *
     * @param string $type
     * @return string
     */
    function getRecommendUserTitle($type)
    {
        $title = array('recommend' => '推荐关注', 'content' => '发表主题最多的人');
        if (isset($title[$type])) {
            return $title[$type];
        }

        return '一些有趣的人';
    }

    /**
     * 发布菜单
     * @param string $type
     */
    function publishMenu($type = 'normal')
    {
        if ($type == 'normal') {
            $tpl_type = 'li';
        } else {
            $tpl_type = 'div';
        }

        $topic_publish_status = &$this->data['topic_public_status'];
        foreach ($this->modeName as $type => $name) {
            $tpl_data = array('name' => $name, 'type' => $type);
            if (!empty($topic_publish_status) && !empty($topic_publish_status[$type])) {
                $this->renderTpl("publish/segment/{$tpl_type}_menu", $tpl_data);
            } else {
                $this->renderTpl("publish/segment/{$tpl_type}_menu", $tpl_data);
            }
        }
    }

    /**
     * 发布链接
     *
     * @param string $type
     * @return string
     */
    function publishLink($type)
    {
        $publish_link = $this->url("publish:{$type}");
        if (isset($this->data['publish_add_topic_name'])) {
            $publish_link .= "#{$this->data['publish_add_topic_name']}";
        }

        return $publish_link;
    }

    /**
     * 分页
     *
     * @param array $data
     * @param string $tpl
     * @return string
     * @throws CoreException
     */
    function page($data = array(), $tpl = 'default')
    {
        if (empty($data['total_page']) || $data['total_page'] == 1) {
            return '';
        }

        if (!isset($data['link'])) {
            throw new CoreException('请指定分页连接参数');
        }
        list($controller, $params) = $data['link'];
        $data['controller'] = $controller;
        $data['params'] = $params;
        $data['anchor'] = isset($data['anchor']) ? $data['anchor'] : '';
        $data['dot'] = isset($page['dot']) ? $page["dot"] : $this->config->get('url', 'dot');
        $this->renderTpl("page/{$tpl}", $data);
    }

    /**
     * 提示消息
     *
     * @param array $data
     * @return string
     */
    function alertMessage($data = array())
    {
        $message_type = array(
            'danger' => '错误',
            'warning' => '出错了',
            'info' => '提示',
            'success' => '成功',
        );

        if (!isset($message_type[$data['alert_type']])) {
            $data['alert_type'] = 'danger';
        }

        $data['can_close'] = true;
        if ($data['alert_type'] == 'danger') {
            $data['can_close'] = false;
            $data['message'] = sprintf('%s (%d)', $this->e($data, 'message'), $data['status']);
        } else {
            $data['message'] = $this->e($data, 'message');
        }

        if (empty($data['wrap_class'])) {
            $data['wrap_class'] = 'col-md-12';
        }

        $data['alert_title'] = $message_type[$data['alert_type']];
        $message = $this->obRenderTpl('fragment/alert/message', $data);
        $this->set('alertMessage', $message);
    }

    /**
     * 获取平台登录配置
     *
     * @return array
     */
    private function getPlatformConfig()
    {
        $pm = new PlatformModule();
        $platformConfig = $pm->getPlatformConfig();
        if (!empty($platformConfig)) {
            foreach ($platformConfig as $name => &$conf) {
                $conf['link'] = $this->url('connect:index', array('t' => $name));
            }
        }

        return $platformConfig;
    }
}
