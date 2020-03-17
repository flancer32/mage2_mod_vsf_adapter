<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product;

/**
 * Data object to represent data structure for product stock info in ElasticSearch index.
 */
class Stock
{
    /** @var bool */
    public $is_in_stock;
    /** @var float quantity */
    public $qty;
    /** @var float quantity increment */
    public $qty_increment;
}
