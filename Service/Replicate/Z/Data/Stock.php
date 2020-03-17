<?php
/**
 * Structure for stock data data used in the code subtree (...\Service\Replicate\...).
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Z\Data;

/**
 * Structure for stock data data used in the code subtree (...\Service\Replicate\...).
 */
class Stock
{
    /** @var int */
    public $productId;
    /** @var string */
    public $productSku;
    /** @var float */
    public $qty;
    /** @var float */
    public $qtyInc;
}
