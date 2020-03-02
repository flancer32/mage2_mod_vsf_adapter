<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Category\A;

use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category as EEsCat;

/**
 * Convert Magento data to ES compatible data for categories.
 */
class Convert
{

    /**
     * @param \Magento\Catalog\Model\Category $mage
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category
     */
    public function mapMageDataToEs($mage)
    {
        $result = new EEsCat();
        // prepare intermediate data
        $id = $mage->getId();
        $slug = "--$id";
        $urlKey = $slug;
        $urlPath = $mage->getUrlPath() . "/$urlKey";
        // prepare target data object
        $result->id = (int)$mage->getId();
        $result->is_active = (bool)$mage->getIsActive();
        $result->level = (int)$mage->getLevel();
        $result->name = $mage->getName();
        $result->parent_id = (int)$mage->getParentId();
        $result->path = $mage->getPath();
        $result->position = (int)$mage->getPosition();
        $result->product_count = (int)$mage->getProductCount();
        $result->slug = $slug;
        $result->url_key = $urlKey;
        $result->url_path = $urlPath;
        return $result;
    }

}
