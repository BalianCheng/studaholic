<?php
namespace lib\Images;

use Exception;

/**
 * @Auth: cmz <393418737@qq.com>
 * Class ImageThumb
 */
class ImageThumb
{
    /**
     * 剪切后图片宽度
     *
     * @var int
     */
    protected $width;

    /**
     * 剪切后图片高度
     *
     * @var int
     */
    protected $height;

    /**
     * 图片质量
     *
     * @var int
     */
    protected $quality = 100;

    /**
     * 文件路径
     *
     * @var string
     */
    protected $save_dir;

    /**
     * 是否对jpg文件启用隔行扫描
     *
     * @var bool
     */
    protected $interlace = false;

    /**
     * 缩略图文件名
     *
     * @var string
     */
    protected $thumb_image_name;

    /**
     * 缩略图资源
     *
     * @var resource
     */
    protected $targetImage;

    /**
     * 等比缩放图资源
     *
     * @var resource
     */
    protected $resizeImage;

    /**
     * 源图资源
     *
     * @var resource
     */
    protected $sourceImage;

    /**
     * 缩略图信息
     *
     * @var array
     */
    protected $resizeImageInfo = array();

    /**
     * 源图信息
     *
     * @var array
     */
    protected $sourceImagesInfo = array();

    function __construct($source_images)
    {
        if (!file_exists($source_images)) {
            throw new Exception('源图不存在');
        }

        $sourceImageInfo = $this->getImageInfo($source_images);
        if (empty($sourceImageInfo)) {
            throw new Exception('无法获取源图信息');
        }

        $this->sourceImagesInfo = $sourceImageInfo;
        $this->sourceImage = $this->createSourceImage($source_images, $sourceImageInfo['file_type']);
    }

    /**
     * 设置文件路径和文件名
     *
     * @param string $dir 缩略图保存路径
     * @param string $thumb_image_name 缩略图名称(不带扩展名)
     * @return $this
     */
    function setFile($dir, $thumb_image_name)
    {
        $this->save_dir = $dir;
        $this->thumb_image_name = $thumb_image_name;

        return $this;
    }

    /**
     * 设置高宽
     *
     * @param int $width
     * @param int $height
     * @return $this
     */
    function setSize($width = 0, $height = 0)
    {
        if ($height == 0) {
            $height = $width;
        }

        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    /**
     * 设置图片质量
     *
     * @param int $quality
     */
    function setQuality($quality)
    {
        $this->quality = $quality;
    }

    /**
     * 隔行扫描设置
     *
     * @param bool $interlace
     */
    function withInterlace($interlace = false)
    {
        $this->interlace = $interlace;
    }

    /**
     * 生成缩略图
     *
     * @param string $full_path
     * @return bool|string
     * @throws Exception
     */
    function thumb(&$full_path = '')
    {
        //等比缩放
        $this->resize();
        //居中剪裁
        $this->copy();
        //保存图片
        return $this->save($full_path);
    }

    /**
     * 等比生成源图的缩略图
     *
     * @throws Exception
     */
    protected function resize()
    {
        if ($this->width == 0 || $this->height == 0) {
            throw new Exception("请设置缩略图高宽");
        }

        //目标图片最大尺寸
        $resizeMax = max($this->width, $this->height);

        //裁剪后的缩略图尺寸
        $sourceWidth = &$this->sourceImagesInfo['width'];
        $sourceHeight = &$this->sourceImagesInfo['height'];
        if ($sourceWidth > $sourceHeight) {
            $resizeImageWidth = ceil($sourceWidth * ($resizeMax / $sourceHeight));
            $resizeImageHeight = $resizeMax;
        } else {
            $resizeImageWidth = $resizeMax;
            $resizeImageHeight = ceil($sourceHeight * ($resizeMax / $sourceWidth));
        }

        $this->resizeImage = $this->createImage($resizeImageWidth, $resizeImageHeight);
        $this->resizeImageInfo = array('width' => $resizeImageWidth, 'height' => $resizeImageHeight);

        //生成源图的缩略图
        if (function_exists('imagecopyresampled')) {
            imagecopyresampled($this->resizeImage, $this->sourceImage, 0, 0, 0, 0, $resizeImageWidth, $resizeImageHeight, $sourceWidth, $sourceHeight);
        } else {
            imagecopyresized($this->resizeImage, $this->sourceImage, 0, 0, 0, 0, $resizeImageWidth, $resizeImageHeight, $sourceWidth, $sourceHeight);
        }

        imagedestroy($this->sourceImage);
    }

    /**
     * 剪裁等比图片, 生成缩略图
     *
     * @throws Exception
     */
    protected function copy()
    {
        $resizeImageWidth = &$this->resizeImageInfo['width'];
        $resizeImageHeight = &$this->resizeImageInfo['height'];

        $x = ($resizeImageWidth - $this->width) / 2;
        $y = ($resizeImageHeight - $this->height) / 2;

        $this->targetImage = $this->createImage($this->width, $this->height);
        imagecopy($this->targetImage, $this->resizeImage, 0, 0, $x, $y, $resizeImageWidth, $resizeImageHeight);
        imagedestroy($this->resizeImage);
    }

    /**
     * 保存缩略图
     *
     * @param string $fileFullPath
     * @return bool
     * @throws Exception
     */
    protected function save(&$fileFullPath = '')
    {
        $suffix = &$this->sourceImagesInfo['suffix'];
        $imageType = &$this->sourceImagesInfo['file_type'];

        $saveDir = $this->getSaveDir();
        $thumbName = $this->getThumbImageName();
        $thumbFileName = $thumbName . $suffix;
        $fileFullPath = $saveDir . $thumbFileName;
        switch ($imageType) {
            case 'jpg':
            case 'jpeg':
            case 'pjpeg':
                $ret = imagejpeg($this->targetImage, $fileFullPath, $this->quality);
                break;

            case 'gif':
                $ret = imagegif($this->targetImage, $fileFullPath);
                break;

            case 'png':
                $ret = imagepng($this->targetImage, $fileFullPath);
                break;

            default:
                $ret = imagegd2($this->targetImage, $fileFullPath);
                break;
        }

        if (!$ret) {
            throw new Exception("保存缩略图失败, 请检查目录权限");
        }

        imagedestroy($this->targetImage);
        return $thumbFileName;
    }

    /**
     * 获取缩略图保存目录
     *
     * @return string
     */
    protected function getSaveDir()
    {
        if (empty($this->save_dir)) {
            $this->save_dir = $this->sourceImagesInfo['file_path'];
        }

        return $this->save_dir;
    }

    /**
     * 获取缩略图名称
     *
     * @return string
     */
    protected function getThumbImageName()
    {
        if (empty($this->thumb_image_name)) {
            $this->thumb_image_name = sprintf('%s-%sx%s', $this->sourceImagesInfo['name'], $this->width, $this->height);
        }

        return $this->thumb_image_name;
    }

    /**
     * 获取图片详细信息
     *
     * @param $image
     * @return array|bool
     */
    protected function getImageInfo($image)
    {
        $image_info = getimagesize($image);
        if (false !== $image_info) {
            $file_info = pathinfo($image);
            $image_suffix = strtolower(image_type_to_extension($image_info[2]));

            return array(
                'width' => $image_info[0],
                'height' => $image_info[1],

                'name' => $file_info['filename'],
                'suffix' => $image_suffix,

                'file_type' => substr($image_suffix, 1),
                'file_name' => $file_info['basename'],
                'file_path' => dirname($image) . DIRECTORY_SEPARATOR,

                'size' => filesize($image),
                'mime' => $image_info['mime'],
            );

        } else {
            return false;
        }
    }

    /**
     * 创建资源图片
     *
     * @param int $width
     * @param int $height
     * @return resource
     * @throws Exception
     */
    protected function createImage($width, $height)
    {
        $imageType = &$this->sourceImagesInfo['file_type'];
        if ($imageType != 'gif' && function_exists('imagecreatetruecolor')) {
            $imageResource = imagecreatetruecolor($width, $height);
        } else {
            $imageResource = imagecreate($width, $height);
        }

        if (!$imageResource) {
            throw new Exception('创建缩略图失败!');
        }

        if ($imageType == 'gif') {
            $background_color = imagecolorallocate($imageResource, 0, 0, 0);
            imagecolortransparent($imageResource, $background_color);
        } elseif ($imageType == 'png') {
            imagealphablending($imageResource, false);
            imagesavealpha($imageResource, true);
        } else {
            imageinterlace($imageResource, (int)$this->interlace);
        }

        return $imageResource;
    }

    /**
     * 创建图片
     *
     * @param $image
     * @param $image_type
     * @return resource
     */
    protected function createSourceImage($image, $image_type)
    {
        switch ($image_type) {
            case 'jpg':
            case 'jpeg':
            case 'pjpeg':
                $res = imagecreatefromjpeg($image);
                break;

            case 'gif':
                $res = imagecreatefromgif($image);
                break;

            case 'png':
                $res = imagecreatefrompng($image);
                break;

            case 'bmp':
                $res = imagecreatefromwbmp($image);
                break;

            default:
                $res = imagecreatefromgd2($image);
                break;
        }

        return $res;
    }

    /**
     * 资源释放
     */
    function __destruct()
    {
        if (is_resource($this->targetImage)) {
            imagedestroy($this->targetImage);
        }

        if (is_resource($this->sourceImage)) {
            imagedestroy($this->sourceImage);
        }

        if (is_resource($this->resizeImage)) {
            imagedestroy($this->resizeImage);
        }
    }
}
