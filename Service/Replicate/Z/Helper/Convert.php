<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Z\Helper;

use Magento\Catalog\Api\Data\ProductAttributeInterface as MageProduct;

/**
 * Convert Magento data to ES compatible data.
 */
class Convert
{

    /**
     * @param \Flancer32\VsfAdapter\Service\Replicate\Z\Data\Attr $attr
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Attr
     */
    public function attributeDataToEs($attr)
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
     * @param \Magento\Catalog\Model\Category $mage
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category
     */
    public function categoryDataToEs($mage)
    {
        $result = new \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category();
        // prepare intermediate data
        $id = $mage->getId();
        $slug = "--$id";
        $urlKey = $slug;
        $urlPath = $mage->getUrlPath() . "/$urlKey";
        // prepare target data object
        $result->id = (int)$mage->getId();
        $result->is_active = (bool)$mage->getIsActive();
        $result->level = (int)$mage->getLevel();
        $result->meta_description = $mage->getData('meta_description');
        $result->meta_title = $mage->getData('meta_title');
        $result->name = $mage->getName();
        $result->parent_id = (int)$mage->getParentId();
        $result->path = $mage->getPath();
        $result->position = (int)$mage->getPosition();
        $result->product_count = (int)$mage->getProductCount();
        $result->slug = $slug;
        $result->url_key = $urlKey;
        $result->url_path = $urlPath;
        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Product $mage
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product
     */
    public function productDataToEs($mage)
    {
        $result = new \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product();
        // prepare intermediate data
        $id = $mage->getId();
        $categoryIds = $mage->getCategoryIds();
        $customOptions = $mage->getCustomOptions();
        $description = $mage->getData(MageProduct::CODE_SHORT_DESCRIPTION);
        $image = $mage->getData('image');
        $isInStock = $mage->isInStock();
        $metaDescription = $mage->getData(MageProduct::CODE_SEO_FIELD_META_DESCRIPTION);
        $metaTitle = $mage->getData(MageProduct::CODE_SEO_FIELD_META_TITLE);
        $name = $mage->getData(MageProduct::CODE_NAME);
        $originalPriceInclTax = $mage->getData('original_price_incl_tax');;
        $parentSku = null; // used in configurable products
        $price = $mage->getData(MageProduct::CODE_PRICE);
        $productLinks = $mage->getProductLinks();
        $qty = $mage->getQty();
        $mage->getStockData();
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
        $result->is_in_stock = $isInStock;
        $result->meta_description = $metaDescription;
        $result->meta_title = $metaTitle;
        $result->name = $name;
        $result->original_price_incl_tax = $originalPriceInclTax;
        $result->parentSku = $parentSku;
        $result->price = $price;
        $result->price_incl_tax = $price;
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
