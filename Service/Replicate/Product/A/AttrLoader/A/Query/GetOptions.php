<?php
/**
 * Query to get raw data for user defined attributes and its values for given store.
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Product\A\AttrLoader\A\Query;

use Flancer32\VsfAdapter\Config as Cfg;
use Magento\Catalog\Api\Data\EavAttributeInterface as ECatAttr;
use Magento\Eav\Api\Data\AttributeInterface as EEavAttr;

/**
 * Query to get raw data for user defined attributes and its values for given store.
 */
class GetOptions
{

    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_ATTR = 'attr';
    const AS_CATALOG = 'cat';
    const AS_OPTION = 'opt';
    const AS_VALUE = 'val';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_ATTR_ID = 'attrId';
    const A_OPTION_ID = 'optionId';
    const A_VALUE = 'value';
    const A_VALUE_STORE_ID = 'valueStoreId';

    /** Bound variables names ('camelCase' naming) */
    const BND_STORE_ID = 'storeId';

    /** Entities are used in the query */
    const E_ATTR = Cfg::ENTITY_EAV_ATTRIBUTE;
    const E_CATALOG = Cfg::ENTITY_CATALOG_EAV_ATTRIBUTE;
    const E_OPTION = Cfg::ENTITY_EAV_ATTRIBUTE_OPTION;
    const E_VALUE = Cfg::ENTITY_EAV_ATTRIBUTE_OPTION_VALUE;

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
     * Build query to get raw data for user defined attributes and its values for given store.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function build()
    {
        $result = $this->conn->select();
        /* define tables aliases for internal usage (in this method) */
        $asAttr = self::AS_ATTR;
        $asCat = self::AS_CATALOG;
        $asOpt = self::AS_OPTION;
        $asVal = self::AS_VALUE;

        /* FROM eav_attribute */
        $tbl = $this->resource->getTableName(self::E_ATTR);    // name with prefix
        $as = $asAttr;    // alias for 'current table' (currently processed in this block of code)
        $cols = [
            self::A_ATTR_ID => EEavAttr::ATTRIBUTE_ID
        ];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* LEFT JOIN catalog_eav_attribute */
        $tbl = $this->resource->getTableName(self::E_CATALOG);
        $as = $asCat;
        $cols = [];
        $cond = "$as." . ECatAttr::ATTRIBUTE_ID . "=$asAttr." . EEavAttr::ATTRIBUTE_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN eav_attribute_option */
        $tbl = $this->resource->getTableName(self::E_OPTION);
        $as = $asOpt;
        $cols = [
            self::A_OPTION_ID => Cfg::E_EAV_ATTR_OPT_A_OPTION_ID
        ];
        $cond = "$as." . Cfg::E_EAV_ATTR_OPT_A_ATTRIBUTE_ID . "=$asAttr." . EEavAttr::ATTRIBUTE_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN eav_attribute_option_value */
        $tbl = $this->resource->getTableName(self::E_VALUE);
        $as = $asVal;
        $cols = [
            self::A_VALUE => Cfg::E_EAV_ATTR_OPT_VAL_A_VALUE,
            self::A_VALUE_STORE_ID => Cfg::E_EAV_ATTR_OPT_VAL_A_STORE_ID
        ];
        $cond = "$as." . Cfg::E_EAV_ATTR_OPT_VAL_A_OPTION_ID . "=$asOpt." . Cfg::E_EAV_ATTR_OPT_A_OPTION_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $byEntityType = "$asAttr." . EEavAttr::ENTITY_TYPE_ID . "=" . (int)Cfg::EAV_TYPE_ID_PROD;
        $byVisible = "$asCat." . ECatAttr::IS_VISIBLE . "=true";
        $byStoreDef = "$asVal." . Cfg::E_EAV_ATTR_OPT_VAL_A_STORE_ID . "=" . (int)Cfg::STORE_ID_ADMIN;
        $byStoreId = "$asVal." . Cfg::E_EAV_ATTR_OPT_VAL_A_STORE_ID . "=:" . self::BND_STORE_ID;
        $byStore = "($byStoreDef) OR ($byStoreId)";
        $result->where("($byEntityType) AND ($byVisible) AND ($byStore)");

        return $result;
    }
}
