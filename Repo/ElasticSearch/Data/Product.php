<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Repo\ElasticSearch\Data;

/**
 * Data object to represent product data structure in ElasticSearch index.
 */
class Product
{
    public $bundle_options;
    public $category_ids;
    public $configurable_options;
    public $custom_options;
    public $description;
    /** @var int */
    public $id;
    public $image;
    public $is_in_stock;
    public $meta_description;
    public $meta_title;
    public $name;
    /**
     * https://github.com/DivanteLtd/vue-storefront/blob/master/docs/guide/cookbook/checklist.md#5-how-vue-storefront-calculates-prices-and-taxes
     *
     * Product Original price (set only if final_price or special_price are lower than price).
     * Optional, if set - it's always price after taxes.
     *
     * @var float
     */
    public $original_price_incl_tax;
    public $parentSku;
    public $price;
    public $price_incl_tax;
    public $product_links;
    public $qty;
    public $sku;
    public $slug;
    public $special_price;
    public $status;
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product\Stock */
    public $stock;
    public $type_id;
    public $updated_at;
    public $url_path;
    public $visibility;
}
