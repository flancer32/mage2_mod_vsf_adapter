<?php
/**
 * Query to get raw data for user defined attributes and its labels for all stores.
 * Filter by $storeId after data being got because the last JOIN doesn't include attrs for missed labels.
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Product\A\AttrLoader\A\Query;

use Flancer32\VsfAdapter\Config as Cfg;
use Magento\Catalog\Api\Data\EavAttributeInterface as ECatAttr;
use Magento\Eav\Api\Data\AttributeInterface as EEavAttr;

/**
 * Query to get raw data for attributes and its labels for all stores.
 * Filter results by $storeId after data being queried because the last JOIN doesn't include attrs for missed labels.
 */
class GetAttrs
{

    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_ATTR = 'attr';
    const AS_CATALOG = 'cat';
    const AS_LABEL = 'lbl';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_CODE = 'code';
    const A_ID = 'id';
    const A_INPUT_TYPE = 'inputType';
    const A_IS_COMPARABLE = 'isComparable';
    const A_IS_USER_DEFINED = 'isUserDefined';
    const A_IS_VISIBLE_ON_FRONT = 'visibleOnFront';
    const A_LABEL = 'label';
    const A_LABEL_DEFAULT = 'labelDef';
    const A_LABEL_STORE_ID = 'labelStoreId';


    /** Entities are used in the query */
    const E_ATTR = Cfg::ENTITY_EAV_ATTRIBUTE;
    const E_CATALOG = Cfg::ENTITY_CATALOG_EAV_ATTRIBUTE;
    const E_LABEL = Cfg::ENTITY_EAV_ATTRIBUTE_LABEL;

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
        $asLbl = self::AS_LABEL;

        /* FROM eav_attribute */
        $tbl = $this->resource->getTableName(self::E_ATTR);    // name with prefix
        $as = $asAttr;    // alias for 'current table' (currently processed in this block of code)
        $cols = [
            self::A_CODE => EEavAttr::ATTRIBUTE_CODE,
            self::A_ID => EEavAttr::ATTRIBUTE_ID,
            self::A_INPUT_TYPE => EEavAttr::FRONTEND_INPUT,
            self::A_LABEL_DEFAULT => EEavAttr::FRONTEND_LABEL,
            self::A_IS_USER_DEFINED => EEavAttr::IS_USER_DEFINED
        ];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* LEFT JOIN catalog_eav_attribute */
        $tbl = $this->resource->getTableName(self::E_CATALOG);
        $as = $asCat;
        $cols = [
            self::A_IS_COMPARABLE => ECatAttr::IS_COMPARABLE,
            self::A_IS_VISIBLE_ON_FRONT => ECatAttr::IS_VISIBLE_ON_FRONT
        ];
        $cond = "$as." . ECatAttr::ATTRIBUTE_ID . "=$asAttr." . EEavAttr::ATTRIBUTE_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN eav_attribute_label */
        $tbl = $this->resource->getTableName(self::E_LABEL);
        $as = $asLbl;
        $cols = [
            self::A_LABEL => Cfg::E_EAV_ATTR_LABEL_A_VALUE,
            self::A_LABEL_STORE_ID => Cfg::E_EAV_ATTR_LABEL_A_STORE_ID
        ];
        $cond = "$as." . Cfg::E_EAV_ATTR_LABEL_A_ATTR_ID . "=$asAttr." . EEavAttr::ATTRIBUTE_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $byEntityType = "$asAttr." . EEavAttr::ENTITY_TYPE_ID . "=" . (int)Cfg::EAV_TYPE_ID_PROD;
        $byVisible = "$asCat." . ECatAttr::IS_VISIBLE . "=true";
        $result->where("($byEntityType)  AND ($byVisible)");

        return $result;
    }
}
