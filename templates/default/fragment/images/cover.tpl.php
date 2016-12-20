<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * small.tpl.php
 */

$content_link = &$data['content_link'];
$loadImage = $this->res('images/load_images.gif');
$images_list = &$data['image_list'];

if($images_list && !empty($images_list[0]) ) {
    $image = &$images_list[0];
    if($content_link) {
        ?>
        <a href="<?php echo $content_link ?>" style="display: block">
            <div style="height:175px;background-image:url('<?php echo $image['image_url'] ?>')"
                 class="background-image-cover"></div>
        </a>
        <?php
    } else {
        ?>
        <div style="height:175px;background-image:url('<?php echo $image['image_url'] ?>')"
             class="background-image-cover"></div>
        <?php
    }
}

