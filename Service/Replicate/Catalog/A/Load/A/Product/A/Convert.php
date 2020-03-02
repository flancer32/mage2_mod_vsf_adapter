<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Product\A;

use Magento\Catalog\Api\Data\ProductAttributeInterface as MageProduct;

/**
 * Convert Magento data to ES compatible data for products and attributes.
 */
class Convert
{

    /**
     * @param \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Data\Attr $attr
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Attr
     */
    public function mapAttrDataToEs($attr)
    {
        $esAttr = new \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Attr();
        $esAttr->attribute_code = $attr->code;
        $esAttr->attribute_id = (int)$attr->id;
        $esAttr->frontend_input = (string)$attr->inputType;
        $esAttr->frontend_label = (string)$attr->label;
        $esAttr->is_comparable = (bool)$attr->isComparable;
        $esAttr->is_user_defined = (bool)$attr->isUserDefined;
        $esAttr->is_visible = true;
        $esAttr->is_visible_on_front = (bool)$attr->isVisibleOnFront;
        return $esAttr;
    }

    /**
     * @param \Magento\Catalog\Model\Product $mage
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product
     */
    public function mapProductDataToEs($mage)
    {
        $result = new \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product();
        // prepare intermediate data
        $id = $mage->getId();
        $categoryIds = $mage->getCategoryIds();
        $customOptions = $mage->getCustomOptions();
        $description = $mage->getData(MageProduct::CODE_SHORT_DESCRIPTION);
        $image = $mage->getData('image');
        $metaDescription = $mage->getData(MageProduct::CODE_SEO_FIELD_META_DESCRIPTION);
        $metaTitle = $mage->getData(MageProduct::CODE_SEO_FIELD_META_TITLE);
        $name = $mage->getData(MageProduct::CODE_NAME);
        $originalPriceInclTax = $mage->getData('original_price_incl_tax');;
        $parentSku = null; // used in configurable products
        $priceInclTax = $mage->getData(MageProduct::CODE_PRICE);
        $productLinks = $mage->getProductLinks();
        $qty = $mage->getQty();
        $sku = $mage->getSku();
        $slug = "--$id";
        $specialPrice = $mage->getData(MageProduct::CODE_SPECIAL_PRICE);
        $status = $mage->getStatus();
        $typeId = $mage->getTypeId();
        $updatedAt = $mage->getUpdatedAt();
        $urlKey = $slug;
        $urlPath = $mage->getUrlPath() . "/$urlKey";
        $visibility = $mage->getVisibility();

        // prepare target data object
        $result->bundle_options = null;
        $result->category_ids = $categoryIds;
        $result->configurable_options = null;
        $result->custom_options = $customOptions;
        $result->description = $description;
        $result->id = $id;
        $result->image = $image;
        $result->meta_description = $metaDescription;
        $result->meta_title = $metaTitle;
        $result->name = $name;
        $result->original_price_incl_tax = $originalPriceInclTax;
        $result->parentSku = $parentSku;
        $result->price_incl_tax = $priceInclTax;
        $result->product_links = [];
        $result->qty = $qty;
        $result->sku = $sku;
        $result->slug = $slug;
        $result->special_price = $specialPrice;
        $result->status = $status;
        $result->stock = [];
        $result->type_id = $typeId;
        $result->updated_at = $updatedAt;
        $result->url_path = $urlPath;
        $result->visibility = $visibility;

        return $result;
    }
}
