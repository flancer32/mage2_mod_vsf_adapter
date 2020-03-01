<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Attr;

/**
 * Data object to represent data structure for product attribute value in ElasticSearch index.
 */
class Option
{
    /** @var string option value */
    public $label;
    /** @var int option ID */
    public $value;
}
