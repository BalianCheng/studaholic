<?php
/**
 * @Auth wonli <wonli@live.com>
 * TitleImagesModule.php
 */

namespace app\forum\modules\title;

use app\forum\modules\common\BaseModule;

/**
 * 内容中的图片处理
 *
 * @Auth wonli <wonli@live.com>
 * Class TitleImagesModule
 * @package modules\title
 */
class TitleImagesModule extends BaseModule
{
    /**
     * 图片位置
     *
     * 1 在主题中
     * 2 在回复中
     */
    const LOCATION_CONTENT = 1;
    const LOCATION_INTERACT = 2;

    /**
     * 保存内容中的图片
     *
     * @param int $title_id
     * @param array $images_list
     * @param int $location 在内容中，还是回复中
     * @param int $page 在主题的第几页
     * @throws \Cross\Exception\CoreException
     */
    function saveImages($title_id, array $images_list, $location, $page)
    {
        $page = (int)$page;
        $location = (int)$location;
        $title_id = (int)$title_id;
        $title_images_id = $title_images_map = array();
        $title_images = $this->link->getAll($this->title_images, '*', array('title_id' => $title_id, 'location' => $location, 'p' => $page));
        if (!empty($title_images)) {
            foreach ($title_images as $t) {
                $title_images_id[] = $t['id'];
                $title_images_map[$t['image_url_md5']] = $t['id'];
            }
        }

        $add_images = $del_images = array();
        if (!empty($images_list)) {
            foreach ($images_list as $image) {
                $image_url = $image['abs_url'];
                $origin_url = $image['origin_url'];
                $image_url_md5 = md5($image_url);

                if (isset($title_images_map[$image_url_md5])) {
                    unset($title_images_map[$image_url_md5]);
                } else {
                    $add_images[] = array(
                        $title_id, $origin_url, $image_url, $image_url_md5, $location, 0, $page
                    );
                }
            }

            if (!empty($title_images_map)) {
                foreach ($title_images_map as $id) {
                    $del_images[] = $id;
                }
            }
        } elseif (!empty($title_images_id)) {
            $del_images = $title_images_id;
        }

        if (!empty($add_images)) {
            $this->link->add($this->title_images, array(
                'fields' => array('title_id', 'origin_url', 'image_url', 'image_url_md5', 'location', 'sync_status', 'p'),
                'values' => $add_images,
            ), true);
        }

        if (!empty($del_images)) {
            $this->link->del($this->title_images, array('id' => array('IN', $del_images)));
        }
    }
}
