<?php
/**
 * Load stock data for given store view and map results by product ID.
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Z;

use Flancer32\VsfAdapter\Service\Replicate\Z\Data\Stock as DStock;
use Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query\GetMsiData as QMsi;
use Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query\GetStockData as QStock;

/**
 * Load stock data for given store view and map results by product ID.
 */
class QtyLoad
{
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query\GetMsiData */
    private $qGetMsi;
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query\GetStockData */
    private $qGetStock;

    public function __construct(
        \Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query\GetMsiData $qGetMsi,
        \Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query\GetStockData $qGetStock
    ) {
        $this->qGetMsi = $qGetMsi;
        $this->qGetStock = $qGetStock;
    }

    /**
     * @param int $storeId
     * @return \Flancer32\VsfAdapter\Service\Replicate\Z\Data\Stock[]
     */
    public function exec($storeId)
    {
        $result = $this->getStockData();
        $msiItems = $this->getMsiData($storeId);
        foreach ($result as $id => $one) {
            $sku = $one->productSku;
            if (isset($msiItems[$sku])) {
                $one->qty = $msiItems[$sku];
            }
        }
        return $result;
    }

    /**
     * Load products quantities from MSI structures for given store view and map results by SKU.
     *
     * @param int $storeId
     * @return array [SKU => QTY]
     */
    private function getMsiData($storeId)
    {
        $result = [];
        $query = $this->qGetMsi->build();
        $conn = $query->getConnection();
        $rs = $conn->fetchAll($query, [QMsi::BND_STORE_ID => $storeId]);
        foreach ($rs as $one) {
            $sku = $one[QMsi::A_SKU];
            $qty = $one[QMsi::A_QTY];
            if (!is_null($sku)) {
                $result[$sku] = $qty;
            }
        }
        return $result;
    }

    /**
     * Load stock data from old stock structures and map results by product id.
     *
     * @return \Flancer32\VsfAdapter\Service\Replicate\Z\Data\Stock[]
     */
    private function getStockData()
    {
        $result = [];
        $query = $this->qGetStock->build();
        $conn = $query->getConnection();
        $rs = $conn->fetchAll($query);
        foreach ($rs as $one) {
            $id = $one[QStock::A_ID];
            $item = new DStock();
            $item->productId = $id;
            $item->productSku = $one[QStock::A_SKU];
            $item->qty = $one[QStock::A_QTY];
            $item->qtyInc = $one[QStock::A_QTY_INC];
            $result[$id] = $item;
        }
        return $result;
    }
}
