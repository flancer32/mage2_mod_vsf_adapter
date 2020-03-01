<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Repo\ElasticSearch\Data;

/**
 * Data object to represent product attribute data structure in ElasticSearch index.
 */
class Attr
{
    /** @var string */
    public $attribute_code;
    /** @var int */
    public $attribute_id;
    /** @var string */
    public $frontend_input;
    /** @var string */
    public $frontend_label;
    /** @var bool */
    public $is_comparable;
    /** @var bool */
    public $is_user_defined;
    /** @var bool */
    public $is_visible;
    /** @var bool */
    public $is_visible_on_front;
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Attr\Option[] */
    public $options;
}
