<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Inventory;


class Response
{
    /** @var int number of disabled products */
    public $disabled;
    /** @var int number of unchanged items */
    public $noops;
    /** @var bool result of the replication */
    public $success;
    /** @var int total number of items */
    public $total;
    /** @var int number of updated items */
    public $updated;
}
