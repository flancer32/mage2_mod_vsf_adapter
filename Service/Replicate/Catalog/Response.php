<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Catalog;


class Response
{
    /** @var int number of attributes being replicated */
    public $attributes;
    /** @var int number of categories being replicated */
    public $categories;
    /** @var int number of products being replicated */
    public $products;
    /** @var bool */
    public $success;
}
