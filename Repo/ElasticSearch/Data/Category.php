<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Repo\ElasticSearch\Data;

/**
 * Data object to represent category data structure in ElasticSearch index.
 */
class Category
{
    /** @var int */
    public $children_count;
    /** @var int */
    public $id;
    /** @var bool */
    public $is_active;
    /** @var int */
    public $level;
    /** @var string */
    public $name;
    /** @var int */
    public $parent_id;
    /** @var string */
    public $path;
    /** @var int */
    public $position;
    /** @var int */
    public $product_count;
    /** @var string */
    public $slug;
    /** @var string */
    public $url_key;
    /** @var string */
    public $url_path;
}
