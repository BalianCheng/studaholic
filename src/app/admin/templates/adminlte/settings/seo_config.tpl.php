<?php
/**
 * @Auth wonli <wonli@live.com>
 * seo_config.tpl.php
 */
$block = <<< BLOCK
    '%s' => array(
        'title' => '%s',
        'keywords' => '%s',
        'description' => '%s'
    ),
BLOCK;

$cache_content = '';
if (!empty($data['config'])) {
    foreach ($data['config'] as $d) {
        $cache_content .= sprintf($block, $d['controller'], $d['title'], $d['keywords'], $d['description']).PHP_EOL;
    }
}

printf('<?php /* cache at %s */' . PHP_EOL . 'return array(' . PHP_EOL . '%s' . PHP_EOL . ');', date('Y-m-d H:i:s'), $cache_content);
