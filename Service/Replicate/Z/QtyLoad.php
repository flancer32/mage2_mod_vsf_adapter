<?php
/**
 * Load products quantities for given store view and map results by SKU.
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Z;


use Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query\GetQty as Query;

/**
 * Load products quantities for given store view and map results by SKU.
 */
class QtyLoad
{
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query\GetQty */
    private $qGetQty;

    public function __construct(
        \Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query\GetQty $qGetQty
    ) {
        $this->qGetQty = $qGetQty;
    }

    /**
     * Load products quantities for given store view and map results by SKU.
     *
     * @param int $storeId
     * @return array [SKU => QTY]
     */
    public function exec($storeId)
    {
        $result = [];
        $query = $this->qGetQty->build();
        $conn = $query->getConnection();
        $rs = $conn->fetchAll($query, [Query::BND_STORE_ID => $storeId]);
        foreach ($rs as $one) {
            $sku = $one[Query::A_SKU];
            $qty = $one[Query::A_QTY];
            if (!is_null($sku)) {
                $result[$sku] = $qty;
            }
        }
        return $result;
    }

}
