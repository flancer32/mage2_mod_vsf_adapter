<?php
/**
 * Load user defined attributes and its values for given store.
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Product\A;

use Flancer32\VsfAdapter\Service\Replicate\Product\A\AttrLoader\A\Query\GetAttrs as QGetAttr;
use Flancer32\VsfAdapter\Service\Replicate\Product\A\AttrLoader\A\Query\GetOptions as QGetOpt;
use Flancer32\VsfAdapter\Service\Replicate\Product\A\Data\Attr as DAttr;
use Flancer32\VsfAdapter\Service\Replicate\Product\A\Data\Attr\Option as DOption;

/**
 * Load user defined attributes and its values for given store.
 */
class AttrLoader
{
    /**
     * This list is extracted from API request, probably configured in "vue-storefront/config/default.json"
     * as "/entities/productList/includeFields".
     */
    private const REQUIRED_ATTRIBUTES = [
        "activity",
        "final_price",
        "id",
        "image",
        "name",
        "new",
        "original_price",
        "original_price_incl_tax",
        "price",
        "price_incl_tax",
        "product_links",
        "sale",
        "sku",
        "special_from_date",
        "special_price",
        "special_price_incl_tax",
        "special_to_date",
        "status",
        "tax_class_id",
        "tier_prices",
        "type_id",
        "url_key",
        "url_path"
    ];
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Product\A\AttrLoader\A\Query\GetAttrs */
    private $qGetAttrs;
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Product\A\AttrLoader\A\Query\GetOptions */
    private $qGetOptions;

    public function __construct(
        \Flancer32\VsfAdapter\Service\Replicate\Product\A\AttrLoader\A\Query\GetAttrs $qGetAttrs,
        \Flancer32\VsfAdapter\Service\Replicate\Product\A\AttrLoader\A\Query\GetOptions $qGetOptions
    ) {
        $this->qGetAttrs = $qGetAttrs;
        $this->qGetOptions = $qGetOptions;
    }


    public function exec($storeId)
    {
        $result = $this->loadAttributes($storeId);
        $result = $this->populateWithOptions($result, $storeId);
        return $result;
    }

    /**
     * Load visible, user defined attributes with labels related to given store view.
     *
     * @param int $storeId
     * @return \Flancer32\VsfAdapter\Service\Replicate\Product\A\Data\Attr[]
     */
    private function loadAttributes($storeId)
    {
        /** @var DAttr[] $result */
        $result = [];
        $query = $this->qGetAttrs->build();
        $conn = $query->getConnection();
        $rs = $conn->fetchAll($query);
        foreach ($rs as $one) {
            $code = $one[QGetAttr::A_CODE];
            $isUserDefined = $one[QGetAttr::A_IS_USER_DEFINED];
            if ($isUserDefined || in_array($code, self::REQUIRED_ATTRIBUTES)) {
                $id = $one[QGetAttr::A_ID];
                if (!isset($result[$id])) {
                    $data = new DAttr();
                    $data->id = $id;
                    $data->code = $code;
                    $data->inputType = $one[QGetAttr::A_INPUT_TYPE];
                    $data->isComparable = $one[QGetAttr::A_IS_COMPARABLE];
                    $data->isUserDefined = $isUserDefined;
                    $data->isVisibleOnFront = $one[QGetAttr::A_IS_VISIBLE_ON_FRONT];
                    $data->label = $one[QGetAttr::A_LABEL_DEFAULT];
                    $data->options = [];
                    $result[$id] = $data;
                }
                // replace attribute label with related to given store view
                if ($one[QGetAttr::A_LABEL_STORE_ID] == $storeId) {
                    $result[$id]->label = $one[QGetAttr::A_LABEL];
                }
            } else {
                // skip attributes that are not user defined and not required
            }
        }
        return $result;
    }

    /**
     * @param DAttr[] $attrs
     * @param int $storeId
     * @return mixed
     */
    private function populateWithOptions($attrs, $storeId)
    {
        $query = $this->qGetOptions->build();
        $conn = $query->getConnection();
        $bind = [
            QGetOpt::BND_STORE_ID => $storeId
        ];
        $rs = $conn->fetchAll($query, $bind);
        /* populate attributes array with options values */
        foreach ($rs as $one) {
            $attrId = $one[QGetOpt::A_ATTR_ID];
            if (!isset($attrs[$attrId])) {
                // skip options for missed attributes
                continue;
            }
            /** @var DOption[] $options */
            $options = $attrs[$attrId]->options ?? [];
            // set option value to array (default or store specific)
            $optionId = $one[QGetOpt::A_OPTION_ID];
            if (!isset($options[$optionId])) {
                $data = new DOption();
                $data->id = $optionId;
                $data->value = $one[QGetOpt::A_VALUE];
                $options[$optionId] = $data;
            } else {
                // replace option's default value if store specific value exists and default value is stored before
                if ($one[QGetOpt::A_VALUE_STORE_ID] == $storeId) {
                    $options[$optionId]->value = $one[QGetOpt::A_VALUE];
                }
            }
            // place options back to attributes array
            $attrs[$attrId]->options = $options;
        }

        return $attrs;
    }
}
