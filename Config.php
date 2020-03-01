<?php
/**
 * Container for module's constants (hardcoded configuration).
 *
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter;

class Config
{

    /**
     * @see `eav_entity_type` table
     */
    const EAV_TYPE_ID_CAT = 3;
    const EAV_TYPE_ID_CUST = 1;
    const EAV_TYPE_ID_CUST_ADDR = 2;
    const EAV_TYPE_ID_INVOICE = 6;
    const EAV_TYPE_ID_MEMO = 7;
    const EAV_TYPE_ID_PROD = 4;
    const EAV_TYPE_ID_SALE = 5;
    const EAV_TYPE_ID_SHIPMENT = 8;

    const ENTITY_CATALOG_EAV_ATTRIBUTE = 'catalog_eav_attribute';
    const ENTITY_EAV_ATTRIBUTE = 'eav_attribute';
    const ENTITY_EAV_ATTRIBUTE_LABEL = 'eav_attribute_label';
    const ENTITY_EAV_ATTRIBUTE_OPTION = 'eav_attribute_option';
    const ENTITY_EAV_ATTRIBUTE_OPTION_VALUE = 'eav_attribute_option_value';
    const E_EAV_ATTR_LABEL_A_ATTR_ID = 'attribute_id';
    const E_EAV_ATTR_LABEL_A_STORE_ID = 'store_id';
    const E_EAV_ATTR_LABEL_A_VALUE = 'value';
    const E_EAV_ATTR_OPT_A_ATTRIBUTE_ID = 'attribute_id';
    const E_EAV_ATTR_OPT_A_OPTION_ID = 'option_id';
    const E_EAV_ATTR_OPT_VAL_A_OPTION_ID = 'option_id';
    const E_EAV_ATTR_OPT_VAL_A_STORE_ID = 'store_id';
    const E_EAV_ATTR_OPT_VAL_A_VALUE = 'value';
    /**
     * Adminhtml menu items.
     */
    const MENU_CATALOG_REPLICATE = self::MODULE . '::catalog_replicate';
    /** This module name. */
    const MODULE = 'Flancer32_VsfAdapter';

    const STORE_ID_ADMIN = 0;
}
