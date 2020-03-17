<?php
/**
 * Query to get raw data for product quantities from old-style Stock Inventory structures.
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query;

use Flancer32\VsfAdapter\Config as Cfg;

/**
 * Query to get raw data for product quantities from old-style Stock Inventory structures.
 */
class GetStockData
{

    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_ITEM = 'i';
    const AS_PRODUCT = 'p';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_ID = 'id';
    const A_QTY = 'qty';
    const A_QTY_INC = 'qtyInc';
    const A_SKU = 'sku';

    /** Entities are used in the query */
    const E_ITEM = Cfg::ENTITY_CATALOGINVENTORY_STOCK_ITEM;
    const E_PRODUCT = Cfg::ENTITY_CATALOG_PRODUCT_ENTITY;

    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    private $conn;

    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
    }

    /**
     * Build query to get raw data for visible product's attributes and its values for given store.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function build()
    {
        $result = $this->conn->select();
        /* define tables aliases for internal usage (in this method) */
        $asItem = self::AS_ITEM;
        $asProduct = self::AS_PRODUCT;

        /* FROM catalog_product_entity */
        $tbl = $this->resource->getTableName(self::E_PRODUCT);    // name with prefix
        $as = $asProduct;    // alias for 'current table' (currently processed in this block of code)
        $cols = [
            self::A_ID => Cfg::E_CATALOG_PRODUCT_A_ENTITY_ID,
            self::A_SKU => Cfg::E_CATALOG_PRODUCT_A_SKU
        ];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* LEFT JOIN cataloginventory_stock_item */
        $tbl = $this->resource->getTableName(self::E_ITEM);
        $as = $asItem;
        $cols = [
            self::A_QTY => Cfg::E_CATINV_STOCK_ITEM_A_QTY,
            self::A_QTY_INC => Cfg::E_CATINV_STOCK_ITEM_A_QTY_INCREMENTS
        ];
        $cond = "$as." . Cfg::E_CATINV_STOCK_ITEM_A_PRODUCT_ID . "=$asProduct." . Cfg::E_CATALOG_PRODUCT_A_ENTITY_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $byStockId = "$asItem." . Cfg::E_CATINV_STOCK_ITEM_A_STOCK_ID . "=" . (int)Cfg::STOCK_ID_DEFAULT;
        $result->where("$byStockId");

        return $result;
    }
}
