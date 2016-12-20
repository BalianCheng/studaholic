<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * ForumView.php
 */

namespace app\admin\views;


use Cross\Core\Helper;
use lib\XSS\Filter;

class ForumView extends AdminView
{
    /**
     * @var array
     */
    protected $siteConfig;

    /**
     * @param $siteConfig
     */
    function setSiteConfig($siteConfig)
    {
        $this->siteConfig = $siteConfig;
    }

    /**
     * 输出主站资源路径
     *
     * @param $src
     * @return string
     */
    function getResource($src)
    {
        if (substr($src, 0, 2) == '//' || substr($src, 0, 4) == 'http') {
            return $src;
        }

        return sprintf('%s/static/%s', rtrim($this->siteConfig['site_homepage'], '/'), $src);
    }

    /**
     * 文本中的图片转换为超链接
     *
     * @param string $txt
     * @param string $lightbox_flag
     * @return mixed
     */
    function imagesToLink($txt, $lightbox_flag='')
    {
        $that = &$this;
        $txt = preg_replace_callback('~<img.*(src="(.*?)"|data-original="(.*?)").*>~i', function (&$match) use ($that, $lightbox_flag) {
            if (!empty($match[3])) {
                $imageUrl = $match[3];
            } else {
                $imageUrl = $match[2];
            }

            return $that->a('[图片]', $imageUrl, array('target' => '_blank', 'data-lightbox' => $lightbox_flag));
        }, $txt);

        $f = new Filter();
        $f->addAllowedTags('a');
        return $f->xss($txt);
    }

    /**
     * @param string $msg
     */
    function alert($msg)
    {
        $this->renderTpl('common/alert', array('msg' => $msg));
    }
}
