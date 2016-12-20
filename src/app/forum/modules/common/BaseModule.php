<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * BaseModule.php
 */

namespace app\forum\modules\common;

use Cross\Core\Helper;
use Cross\MVC\Module;
use lib\XSS\Filter;
use DOMDocument;

/**
 * @Auth: cmz <393418737@qq.com>
 * Class BaseModule
 * @package modules\common
 */
abstract class BaseModule extends Module
{
    const VERSION = '1.0.1';

    const TYPE_QUESTION = 1;
    const TYPE_POSTS = 2;
    const TYPE_ARTICLE = 3;

    public static $typeMap = array(
        self::TYPE_QUESTION => 'question',
        self::TYPE_ARTICLE => 'article',
        self::TYPE_POSTS => 'posts',
    );

    //标题
    protected $title;
    protected $title_images;

    //推荐
    protected $recommend_user;
    protected $recommend_title;

    //收藏及分类
    protected $collections;
    protected $collections_category;

    //关注内容,话题及用户
    protected $following_content;
    protected $following_topic;
    protected $following_user;
    protected $following_act;

    //话题
    protected $topic;
    protected $topic_editor;
    protected $topic_title_id;
    protected $topic_following;

    //帖子及回复
    protected $posts;
    protected $posts_content;
    protected $reply;
    protected $reply_up;
    protected $reply_comment;

    //问题及答案
    protected $questions;
    protected $answers;
    protected $answers_comment;
    protected $answers_stat;
    protected $answers_stand;

    //文章
    protected $articles;
    protected $articles_income;
    protected $articles_content;
    protected $articles_category;
    protected $articles_comment;
    protected $articles_stand;

    //用户
    protected $user;
    protected $user_openid;
    protected $user_act_log;

    //点击数
    protected $hits_posts;
    protected $hits_articles;
    protected $hits_questions;

    //邀请相关
    protected $invite;
    protected $invite_code;

    //消息相关
    protected $message;

    //seo
    protected $seo;

    //举报
    protected $report;

    function __construct()
    {
        parent::__construct();
        $prefix = $this->getPrefix();

        $this->title = $prefix . 'title';
        $this->title_images = $prefix . 'title_images';

        $this->recommend_user = $prefix . 'recommend_user';
        $this->recommend_title = $prefix . 'recommend_title';

        $this->collections = $prefix . 'collections';
        $this->collections_category = $prefix . 'collections_category';

        $this->following_act = $prefix . 'following_act';
        $this->following_user = $prefix . 'following_user';
        $this->following_topic = $prefix . 'following_topic';
        $this->following_content = $prefix . 'following_content';

        $this->topic = $prefix . 'topic';
        $this->topic_editor = $prefix . 'topic_editor';
        $this->topic_title_id = $prefix . 'topic_title_id';
        $this->topic_following = $prefix . 'topic_following';

        $this->posts = $prefix . 'posts';
        $this->posts_content = $prefix . 'posts_content';
        $this->reply = $prefix . 'reply';
        $this->reply_up = $prefix . 'reply_up';
        $this->reply_comment = $prefix . 'reply_comment';

        $this->questions = $prefix . 'questions';
        $this->answers = $prefix . 'answers';
        $this->answers_comment = $prefix . 'answers_comment';
        $this->answers_stat = $prefix . 'answers_stat';
        $this->answers_stand = $prefix . 'answers_stand';

        $this->articles = $prefix . 'articles';
        $this->articles_income = $prefix . 'articles_income';
        $this->articles_content = $prefix . 'articles_content';
        $this->articles_category = $prefix . 'articles_category';
        $this->articles_comment = $prefix . 'articles_comment';
        $this->articles_stand = $prefix . 'articles_stand';

        $this->user = $prefix . 'user';
        $this->user_openid = $prefix . 'user_openid';
        $this->user_act_log = $prefix . 'user_act_log';

        $this->hits_posts = $prefix . 'hits_posts';
        $this->hits_articles = $prefix . 'hits_articles';
        $this->hits_questions = $prefix . 'hits_questions';

        $this->invite = $prefix . 'invite';
        $this->invite_code = $prefix . 'invite_code';

        $this->message = $prefix . 'message';

        $this->seo = $prefix . 'seo';

        $this->report = $prefix . 'report';
    }

    /**
     * 过滤不安全的HTML标签
     *
     * @param string $content
     * @return mixed|string
     */
    protected static function filterHTML($content)
    {
        $allowedTags = array('p', 'br', 'img', 'a', 'i', 'b', 'u', 'li', 'ol', 'blockquote', 'pre', 'div', 'h3', 'h4', 'h5');
        return self::filterContent($content, $allowedTags);
    }

    /**
     * 获取文章摘要
     *
     * @param string $content
     * @return string
     */
    protected static function getSummary($content)
    {
        $content = self::filterContent($content, array(), array());
        return Helper::subStr($content, 250);
    }

    /**
     * 字符过滤
     *
     * @param string $content
     * @param array $allowedTags
     * @param array $allowedProtocols
     * @return mixed|string
     */
    protected static function filterContent(
        $content,
        $allowedTags = array('img', 'p'),
        $allowedProtocols = array('http', 'https')
    )
    {
        $filter = new Filter();
        $filter->addAllowedProtocols($allowedProtocols);
        $filter->addAllowedTags($allowedTags);
        return $filter->xss($content);
    }

    /**
     * 先转义再从数组中取值
     *
     * @param array $data
     * @param string $key
     * @param bool|string $strip_tags
     * @return string
     */
    protected static function getArrayData(array $data, $key, $strip_tags = true)
    {
        if (isset($data[$key]) && $strip_tags) {
            return self::getEntitiesData(strip_tags($data[$key]));
        } elseif (isset($data[$key])) {
            return self::getEntitiesData(self::filterHTML($data[$key]));
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
        return htmlspecialchars($string, ENT_NOQUOTES, 'utf-8', false);
    }

    /**
     * 获取内容并处理内容中的图片
     *
     * @param string $content
     * @param array $images_list
     * @return mixed
     */
    protected static function getContent($content, &$images_list = array())
    {
        set_time_limit(300);
        $content = str_replace(array('<p></p>', '<p><br></p>', '<p><br/></p>'), array('', '', ''), $content);
        $content = self::filterHTML($content);

        $DOCUMENT = new DOMDocument();
        @$DOCUMENT->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

        $images_list = array();
        $loadImageSrc = self::getResRelUrl('images/load_images.gif');
        foreach ($DOCUMENT->getElementsByTagName('img') as $imgNode) {
            if ($imgNode->hasAttribute('data-original')) {
                $origin_url = $imgNode->getAttribute('origin');
                $data_original = $imgNode->getAttribute('data-original');
            } else {
                //上传图片
                if ($imgNode->hasAttribute('origin')) {
                    $origin_url = $imgNode->getAttribute('origin');
                    $data_original = self::getResRelUrl($origin_url);
                } else {
                    //内容中的图片
                    $origin_url = '';
                    $data_original = $imgNode->getAttribute('src');
                }

                $imgNode->setAttribute('class', 'lazy');
            }

            //处理非本地图片
            if (!$imgNode->hasAttribute('local')) {
                $source_src = $data_original;
                $status = self::checkImage($data_original);
                $imgNode->setAttribute('local', 1);
                if (!$status) {
                    $imgNode->setAttribute('alt', 'bad image');
                    $imgNode->setAttribute('title', 'bad image');
                    $imgNode->setAttribute('source-src', $source_src);
                    $data_original = self::getResRelUrl('images/error_image.png');
                } else {
                    $origin_url = $data_original;
                    $data_original = self::getResRelUrl($data_original);
                }
            }

            $imgNode->setAttribute('src', $loadImageSrc);
            $imgNode->setAttribute('data-original', $data_original);
            $imgNode->setAttribute('style', 'display:block');
            $images_list[] = array(
                'abs_url' => $data_original,
                'origin_url' => $origin_url
            );
        }

        $content = $DOCUMENT->saveHTML($DOCUMENT->documentElement);
        $content = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $content);
        return trim($content);
    }

    /**
     * 获取资源相对路径
     *
     * @param string $res_url
     * @param string $res_dir
     * @return string
     */
    static protected function getResRelUrl($res_url, $res_dir = 'static')
    {
        static $res_base_url = null;
        if (null === $res_base_url) {
            $request = self::$app_delegate->getConfig()->get('url', 'request');
            $res_base_url = rtrim($request, '/') . '/' . $res_dir . '/';
        }

        return $res_base_url . $res_url;
    }

    /**
     * 检查图片
     *
     * @param string $src
     * @return bool
     */
    static function checkImage(&$src)
    {
        $stream = stream_context_set_default(array('http' => array('timeout' => 5)));
        $headers = @get_headers($src, 1);
        if ($headers) {
            //http status
            $isMatch = preg_match("~HTTP/.*\s(\d+)\s~i", $headers[0], $matches);
            if (!$isMatch || $matches[1] != 200) {
                return false;
            }

            //content type
            $content_type = &$headers['Content-Type'];
            @list($mime_type,) = explode(';', $content_type);
            $allow_mime_type = array(
                'image/gif' => '.gif',
                'image/jpeg' => '.jpg',
                'image/png' => '.png',
                'image/svg+xml' => '.svg',
                'image/bmp' => '.bmp',
            );

            $mime_type = trim($mime_type);
            if (isset($allow_mime_type[$mime_type])) {
                $image_ext = $allow_mime_type[$mime_type];
            } else {
                return false;
            }

            //save remote image
            $save_file = 'remote_images' . date('/Y/m/d/His_') . mt_rand(10000, 99999) . $image_ext;
            $save_path = self::$app_delegate->getConfig()->get('static', 'path') . $save_file;
            Helper::mkfile($save_path);

            $file_content = file_get_contents($src, false, $stream);
            if ($file_content) {
                $src = $save_file;
                file_put_contents($save_path, $file_content, LOCK_EX);
                return true;
            }

            return false;
        } else {
            return false;
        }
    }

    /**
     * 获取类型id
     *
     * @param string $modeName
     * @return int
     */
    static function getModeType($modeName)
    {
        $modeNameConfig = array(
            'question' => self::TYPE_QUESTION,
            'article' => self::TYPE_ARTICLE,
            'posts' => self::TYPE_POSTS
        );

        if (isset($modeNameConfig[$modeName])) {
            return $modeNameConfig[$modeName];
        }

        //所有类型
        return 0;
    }

    /**
     * 安全的获取用分隔符分隔的字符串ID
     *
     * @param $string
     * @param bool $return_array
     * @param string $separate
     * @return array|string
     */
    static function getInputSeparateID($string, $return_array = false, $separate = ',')
    {
        $array = array_map('intval', explode($separate, trim($string, $separate)));
        if ($return_array) {
            return $array;
        }

        return implode($separate, $array);
    }

    /**
     * 检查IP是否已经被屏蔽
     *
     * @param string $ip
     * @return bool 已经被屏蔽返回true
     */
    protected function ipIsBlock($ip = '')
    {
        $block_file = $this->getFilePath('config::ip_block.config.php');
        if (!file_exists($block_file)) {
            $default_block_file = $this->getFilePath('config::default.ip_block.config.php');
            copy($default_block_file, $block_file);
        }

        $block_ip_config = $this->loadConfig('ip_block.config.php')->getAll();
        if (empty($block_ip_config)) {
            return false;
        }

        $block_ip_set = array();
        $set_block = function (&$set, $key) {
            if (!isset($set[$key])) {
                $set[$key] = $key;
            }
        };

        foreach ($block_ip_config as $block) {
            list($s1, $s2, $s3, $s4) = explode('.', $block);
            $set_block($block_ip_set[0], $s1);
            $set_block($block_ip_set[1], $s2);
            $set_block($block_ip_set[2], $s3);
            $set_block($block_ip_set[3], $s4);
        }

        $score = 0;
        $current_ip = explode('.', $ip);
        foreach ($current_ip as $k => $s) {
            $block = &$block_ip_set[$k];
            if (isset($block['*'])) {
                $score += 1;
            } elseif (isset($block[$s])) {
                $score += 1;
            }
        }

        return $score == 4;
    }

    /**
     * 获取主题中包含的图片
     *
     * @param array $title_ids
     * @return array
     * @throws \Cross\Exception\CoreException
     */
    protected function getTitlesImages(array $title_ids)
    {
        if (!empty($title_ids)) {
            return $this->link->select('title_id, image_url, image_url_md5, sync_status')->from($this->title_images)
                ->where(array('title_id' => array('IN', $title_ids), 'location' => 1))
                ->stmt()->fetchAll(\PDO::FETCH_GROUP | \PDO::FETCH_ASSOC);
        }

        return array();
    }

}
