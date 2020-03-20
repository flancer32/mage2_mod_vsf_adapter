<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product;

/**
 * This is my own data structure. VSF does not use 'tier prices' or 'group prices' yet ('default' theme
 * of 'vue-storefront' knows nothing about these prices), but my project does.
 *
 * You should add processing of this data by yourself if you wish:
 *      - plugin to add these prices to 'product' data in your Magento module
 *          (\Flancer32\VsfAdapter\Service\Replicate\Z\Helper\Convert::productDataToEs);
 *      - components in your VSF theme to process group prices on the front;
 */
class GroupPrice
{
    /** @var int */
    public $group_id;
    /** @var float */
    public $price;
}
