<?php
/**
 * @Auth wonli <wonli@live.com>
 * SeoModule.php
 */

namespace app\forum\modules\common;


class SeoModule extends BaseModule
{
    /**
     * @return mixed
     */
    function getSeoConfig()
    {
        return $this->link->getAll($this->seo, '*');
    }

    /**
     * 更新SEO配置
     * @param int $id
     * @param $config
     * @return bool
     */
    function updateSeoConfig($id, $config)
    {
        return $this->link->update($this->seo, $config, array('id' => (int)$id));
    }
}
