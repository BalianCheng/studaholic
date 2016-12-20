<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * Web.php
 */

namespace app\forum\controllers;

use app\forum\modules\common\BaseModule;
use Cross\Exception\CoreException;
use app\forum\views\ForumView;
use lib\Images\UploadImages;
use Cross\MVC\Controller;
use Cross\Core\Config;
use Cross\Core\Helper;

/**
 * app基类
 *
 * @Auth: cmz <393418737@qq.com>
 * Class Forum
 * @package app\forum\controllers
 *
 * @property ForumView $view
 */
abstract class Forum extends Controller
{
    /**
     * 登录用户uid
     *
     * @var int
     */
    protected $uid = 0;

    /**
     * 登录标识
     *
     * @var bool
     */
    protected $isLogin = false;

    /**
     * @var Config
     */
    protected $siteConfig;

    /**
     * 内容导航菜单
     *
     * @var array
     */
    protected $contentMenu = array();

    /**
     * @var array
     */
    protected $loginUser = array('uid' => '', 'account' => '', 'nickname' => '', 'avatar' => '');

    /**
     * @var array
     */
    protected $data = array('status' => 1);

    /**
     * 获取推荐话题
     *
     * Forum constructor.
     */
    function __construct()
    {
        parent::__construct();

        //获取站点配置
        $this->siteConfig = $this->getSiteConfig();
        $this->view->setSiteConfig($this->siteConfig);

        //重新设置REWRITE
        $this->config->set('url', array(
            'rewrite' => $this->siteConfig->get('rewrite')
        ));

        //获取seo配置
        $seoConfig = $this->getSeoConfig();
        $this->view->setSeoConfig($seoConfig);

        //模块名称配置
        $mode_name = $this->loadConfig('mode_name.config.php')->getAll();
        $this->view->setModeName($mode_name);

        //设置目录目录
        $this->view->setTplDir($this->siteConfig->get('tpl_dir'));
        $this->view->setTplBasePath(dirname($this->config->get('static', 'path')) . DIRECTORY_SEPARATOR . 'templates');

        //重置默认配置中的加密参数key
        $this->config->set('encrypt', $this->siteConfig->get('encrypt'));

        //初始化插件
        $this->initPlugs();

        //获取登陆信息
        $this->initLoginInfo();
        $this->data['isLogin'] = $this->isLogin;
        $this->data['loginUser'] = $this->loginUser;
        $this->data['version'] = BaseModule::VERSION;

        //内容导航菜单
        $this->contentMenu = $this->loadConfig('nav_menu.config.php')->getAll();
        $this->view->setContentNavMenu($this->contentMenu);
        $this->response->setHeader("Powered-By:Studaholic {$this->data['version']}");
    }

    /**
     * 生成表单提交令牌
     */
    protected function makeCSRFToken()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $token = Helper::random(12);
        $this->data['csrf_token'] = $_SESSION['csrf_token'] = $token;
    }

    /**
     * 验证表单令牌
     *
     * @param $token
     * @return bool
     */
    protected function checkCSRFToken($token)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if ((isset($_SESSION['csrf_token'])) && $token === $_SESSION['csrf_token']) {
            unset($_SESSION['csrf_token']);
            return true;
        }
        return false;
    }

    /**
     * @see FrameBase::result()
     *
     * @param int $status
     * @param string $message
     * @param bool $json_encode
     * @return array|string
     * @throws CoreException
     */
    function result($status = 1, $message = 'ok', $json_encode = false)
    {
        $message = $this->getStatusMessage($status);
        return parent::result($status, $message);
    }

    /**
     * 初始化登陆信息
     */
    final function initLoginInfo()
    {
        $userInfo = $this->getAuth('u', true);
        if ($userInfo) {
            $this->uid = $userInfo['uid'];
            $this->isLogin = true;
            $this->loginUser = $userInfo;
        }
    }

    /**
     * 初始化插件
     */
    final function initPlugs()
    {

    }

    /**
     * 安全的获取POST数据
     *
     * @param string $key
     * @param bool $strip_tags
     * @return string
     */
    protected static function postData($key, $strip_tags = true)
    {
        if (isset($_POST[$key]) && $strip_tags) {
            return self::getEntitiesData(strip_tags(trim($_POST[$key])));
        } elseif (isset($data[$key])) {
            return self::getEntitiesData(trim($_POST[$key]));
        }

        return '';
    }

    /**
     * 转码html实体
     *
     * @param $string
     * @return string
     */
    protected static function getEntitiesData($string)
    {
        return htmlentities($string, ENT_COMPAT, 'utf-8');
    }

    /**
     * @param null $data
     * @param null $method
     * @param int $http_response_status
     * @throws CoreException
     */
    function display($data = null, $method = null, $http_response_status = 200)
    {
        if (!isset($data['status'])) {
            throw new CoreException('必须设置status');
        }

        if ($data['status'] != 1 && empty($data['message'])) {
            $data['message'] = $this->getStatusMessage($data['status']);
        }

        parent::display($data, $method, $http_response_status);
    }

    /**
     * @see Controller::to()
     *
     * @param null $controller
     * @param null $params
     * @param string $hash 带#的字符串
     */
    function toHash($controller = null, $params = null, $hash = '')
    {
        $url = $this->view->url($controller, $params);
        if ($hash) {
            $url .= $hash;
        }

        $this->redirect($url);
    }

    /**
     * 提示消息
     *
     * @param null $data
     * @param string $type
     * @param string $wrap_class
     * @return string
     */
    function alertMessage($data = null, $type = 'danger', $wrap_class = 'col-md-12')
    {
        if (!is_array($data)) {
            if (is_int($data)) {
                $data = array(
                    'status' => $data,
                    'message' => $this->getStatusMessage($data),
                );
            } else {
                $data = array(
                    'status' => -1,
                    'message' => $data,
                );
            }
        }

        $data['alert_type'] = $type;
        $data['wrap_class'] = $wrap_class;
        return $this->view->alertMessage($data);
    }

    /**
     * 登录后跳转
     *
     * @param string $controller
     */
    function loginAfterReturn($controller = '')
    {
        if (empty($controller)) {
            $current_url = $this->request->getCurrentUrl(false);
            if ($current_url) {
                $params = array('back' => urlencode(base64_encode($current_url)));
            } else {
                $params = array();
            }
        } else {
            $full_url = $this->view->url($controller);
            $host_info = $this->request->getHostInfo();
            $back_url = substr($full_url, strlen($host_info));

            $params = array('back' => urlencode(base64_encode($back_url)));
        }

        $this->to('user:login', $params);
    }

    /**
     * 更新cookie
     *
     * @param $data
     */
    function updateUserCookie(array $data)
    {
        $cookie = $this->loginUser;
        foreach ($data as $key => $value) {
            if (isset($cookie[$key])) {
                $cookie[$key] = $value;
            }
        }

        $this->setAuth('u', $cookie);
    }

    /**
     * 上传头像
     *
     * @param int $uid
     * @return string
     * @throws \Cross\Exception\CoreException
     */
    function uploadAvatar($uid)
    {
        if (empty($_FILES['avatar'])) {
            return '';
        }

        $save_dir = Helper::getPath($uid, 'avatar');
        $save_path = $this->getFilePath("static::{$save_dir}");

        Helper::createFolders($save_path);
        $avatar_name = date('YmdHi');
        $IM = new UploadImages('avatar', $avatar_name);
        $IM->setSavePath($save_path);
        $upload_info = $IM->thumb(array(128), false, true);

        if ($upload_info['status'] != 'ok') {
            return '';
        }

        return $save_dir . $upload_info['message']['thumb'][0];
    }

    /**
     * 上传二维码
     *
     * @param int $uid
     * @return string
     */
    function uploadQR($uid)
    {
        if (empty($_FILES['qr'])) {
            return '';
        }

        $save_dir = Helper::getPath($uid, 'qr');
        $save_path = $this->getFilePath("static::{$save_dir}");

        Helper::createFolders($save_path);
        $file_name = $uid . '-qr';
        $IM = new UploadImages('qr', $file_name);
        $IM->setSavePath($save_path);
        $upload_info = $IM->thumb();

        if ($upload_info['status'] != 'ok') {
            return '';
        }

        return $save_dir . $upload_info['message']['ori'] . '?' . time();
    }

    /**
     * 获取站点配置
     *
     * @return Config
     */
    private function getSiteConfig()
    {
        return $this->loadConfig('site.config.php');
    }

    /**
     * 获取seo配置
     *
     * @return Config
     */
    private function getSeoConfig()
    {
        return $this->loadConfig('seo.config.php');
    }

    /**
     * 获取状态码信息
     *
     * @param $status
     * @return string
     * @throws CoreException
     */
    private function getStatusMessage($status)
    {
        $message = '';
        if ($status != 1) {
            static $notice = null;
            if ($notice === null) {
                $notice = $this->parseGetFile('config::notice.config.php');
            }

            if (isset($notice[$status])) {
                $message = $notice[$status];
            }
        }

        return $message;
    }

    /**
     * @return mixed
     */
    abstract function index();
}
