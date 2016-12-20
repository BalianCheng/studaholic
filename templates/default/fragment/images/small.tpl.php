<?php
/**
 * @Auth: cmz <393418737@qq.com>
 * small.tpl.php
 */

$max_image = 5;
if (!empty($data['max_image'])) {
    $max_image = (int)$data['max_image'];
}

$style='';

$content_link = &$data['content_link'];
$loadImage = $this->res('images/load_images.gif');
if(!empty($data['image_list'])) {
    foreach($data['image_list'] as $i => $image) {
        if($i >= $max_image) {
            break;
        }

        $img = $this->img($loadImage, array(
            'style' => $style,
            'class' => 'lazy',
            'data-original' => $image['image_url'],
        ));

        echo $this->a($img, $content_link);
    }
}


