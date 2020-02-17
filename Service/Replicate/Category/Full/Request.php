<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Category\Full;


class Request
{
    /** @var string Prefix to compose name for ElasticSearch index ('vsf_msk' => 'vsf_msk_category') */
    public $indexPrefix;
    /** @var int Store view ID to select data for. */
    public $storeId;
}
