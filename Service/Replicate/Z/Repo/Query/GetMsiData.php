<?php
/**
 * Query to get raw data for product quantities from MSI structures.
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query;

use Flancer32\VsfAdapter\Config as Cfg;
use Magento\Framework\DB\Sql\Expression as AnExpression;

/**
 * Query to get raw data for product quantities from Multi Stock Inventory structures.
 */
class GetMsiData
{

    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_CHANNEL = 'c';
    const AS_ITEM = 'i';
    const AS_STORE = 's';
    const AS_WEBSITE = 'w';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_QTY = 'qty';
    const A_SKU = 'sku';

    /** Bound variables names ('camelCase' naming) */
    const BND_STORE_ID = 'storeId';

    /** Entities are used in the query */
    const E_CHANNEL = Cfg::ENTITY_INVENTORY_STOCK_SALES_CHANNEL;
    const E_ITEM = Cfg::ENTITY_INVENTORY_SOURCE_ITEM;
    const E_STORE = Cfg::ENTITY_STORE;
    const E_WEBSITE = Cfg::ENTITY_STORE_WEBSITE;

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
        $asChannel = self::AS_CHANNEL;
        $asItem = self::AS_ITEM;
        $asStore = self::AS_STORE;
        $asWebsite = self::AS_WEBSITE;

        /* FROM eav_attribute */
        $tbl = $this->resource->getTableName(self::E_STORE);    // name with prefix
        $as = $asStore;    // alias for 'current table' (currently processed in this block of code)
        $cols = [];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* LEFT JOIN store_website */
        $tbl = $this->resource->getTableName(self::E_WEBSITE);
        $as = $asWebsite;
        $cols = [];
        $cond = "$as." . Cfg::E_STORE_WEBSITE_A_WEBSITE_ID . "=$asStore." . Cfg::E_STORE_A_WEBSITE_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN inventory_stock_sales_channel */
        $tbl = $this->resource->getTableName(self::E_CHANNEL);
        $as = $asChannel;
        $cols = [];
        $cond = "$as." . Cfg::E_INV_STOCK_SALES_CHANNEL_A_CODE . "=$asWebsite." . Cfg::E_STORE_WEBSITE_A_CODE;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN inventory_source_item */
        $tbl = $this->resource->getTableName(self::E_ITEM);
        $as = $asItem;
        $cols = [
            self::A_SKU => Cfg::E_INV_SOURCE_ITEM_A_SKU,
            self::A_QTY => $this->getQtySum()
        ];
        $cond = "$as." . Cfg::E_INV_SOURCE_ITEM_A_SOURCE_CODE . "=$asChannel." . Cfg::E_INV_STOCK_SALES_CHANNEL_A_CODE;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $byStockId = "$asStore." . Cfg::E_STORE_A_STORE_ID . "=:" . self::BND_STORE_ID;
        $result->where("$byStockId");
        $result->group($asItem . '.' . Cfg::E_INV_SOURCE_ITEM_A_SKU);

        return $result;
    }

    private function getQtySum()
    {
        $exp = "SUM(" . self::AS_ITEM . '.' . Cfg::E_INV_SOURCE_ITEM_A_QUANTITY . ")";
        return new AnExpression($exp);
    }
}
