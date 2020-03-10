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
    const ENTITY_INVENTORY_SOURCE_ITEM = 'inventory_source_item';
    const ENTITY_INVENTORY_STOCK_SALES_CHANNEL = 'inventory_stock_sales_channel';
    const ENTITY_STORE = 'store';
    const ENTITY_STORE_WEBSITE = 'store_website';

    const E_EAV_ATTR_LABEL_A_ATTR_ID = 'attribute_id';
    const E_EAV_ATTR_LABEL_A_STORE_ID = 'store_id';
    const E_EAV_ATTR_LABEL_A_VALUE = 'value';
    const E_EAV_ATTR_OPT_A_ATTRIBUTE_ID = 'attribute_id';
    const E_EAV_ATTR_OPT_A_OPTION_ID = 'option_id';
    const E_EAV_ATTR_OPT_VAL_A_OPTION_ID = 'option_id';
    const E_EAV_ATTR_OPT_VAL_A_STORE_ID = 'store_id';
    const E_EAV_ATTR_OPT_VAL_A_VALUE = 'value';
    const E_INV_SOURCE_ITEM_A_QUANTITY = 'quantity';
    const E_INV_SOURCE_ITEM_A_SKU = 'sku';
    const E_INV_SOURCE_ITEM_A_SOURCE_CODE = 'source_code';
    const E_INV_STOCK_SALES_CHANNEL_A_CODE = 'code';
    const E_STORE_A_STORE_ID = 'store_id';
    const E_STORE_A_WEBSITE_ID = 'website_id';
    const E_STORE_WEBSITE_A_CODE = 'code';
    const E_STORE_WEBSITE_A_WEBSITE_ID = 'website_id';
    /**
     * Adminhtml menu items (./etc/adminhtml/menu.xml).
     */
    const MENU_REPLICATE_CATALOG = self::MODULE . '::replicate_catalog';
    const MENU_REPLICATE_INVENTORY = self::MODULE . '::replicate_inventory';

    /** This module name. */
    const MODULE = 'Flancer32_VsfAdapter';

    const STORE_ID_ADMIN = 0;
}
