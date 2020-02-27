<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate;

use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product as EProduct;
use Flancer32\VsfAdapter\Service\Replicate\Product\Request as ARequest;
use Flancer32\VsfAdapter\Service\Replicate\Product\Response as AResponse;
use Magento\Catalog\Api\Data\ProductAttributeInterface as MageProduct;

/**
 * Completely delete all product data from ElasticSearch and index new ones.
 */
class Product
{
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter */
    private $adapterEs;
    /** @var \Magento\Framework\Api\Search\SearchCriteriaInterfaceFactory */
    private $buildCriteria;
    /** @var \Magento\Framework\Api\FilterBuilder */
    private $buildFilter;
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Product */
    private $daoProd;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $mgrStore;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProd;

    public function __construct(
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Magento\Framework\Api\SearchCriteriaBuilder $buildCriteria,
        \Magento\Framework\Api\FilterBuilder $buildFilter,
        \Magento\Store\Model\StoreManagerInterface $mgrStore,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd,
        \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter $adapterEs,
        \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Product $daoProd
    ) {
        $this->logger = $logger;
        $this->buildCriteria = $buildCriteria;
        $this->buildFilter = $buildFilter;
        $this->mgrStore = $mgrStore;
        $this->repoProd = $repoProd;
        $this->adapterEs = $adapterEs;
        $this->daoProd = $daoProd;
    }

    /**
     * Convert product data from Magento format to ElasticSearch format.
     *
     * @param \Magento\Catalog\Model\Product[] $mageProds
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product[]
     */
    private function convertMageToEs($mageProds)
    {
        $result = [];
        foreach ($mageProds as $one) {
            // prepare intermediate data
            $id = $one->getId();
            $categoryIds = $one->getCategoryIds();
            $customOptions = $one->getCustomOptions();
            $description = $one->getData(MageProduct::CODE_SHORT_DESCRIPTION);
            $image = $one->getData('image');
            $metaDescription = $one->getData(MageProduct::CODE_SEO_FIELD_META_DESCRIPTION);
            $metaTitle = $one->getData(MageProduct::CODE_SEO_FIELD_META_TITLE);
            $name = $one->getData(MageProduct::CODE_NAME);
            $originalPriceInclTax = $one->getData('original_price_incl_tax');;
            $parentSku = null; // used in configurable products
            $priceInclTax = $one->getData(MageProduct::CODE_PRICE);
            $productLinks = $one->getProductLinks();
            $qty = $one->getQty();
            $sku = $one->getSku();
            $slug = "--$id";
            $specialPrice = $one->getData(MageProduct::CODE_SPECIAL_PRICE);
            $status = $one->getStatus();
            $typeId = $one->getTypeId();
            $updatedAt = $one->getUpdatedAt();
            $urlKey = $slug;
            $urlPath = $one->getUrlPath() . "/$urlKey";
            $visibility = $one->getVisibility();
            // prepare target data object
            $esItem = new EProduct();
            $esItem->bundle_options = null;
            $esItem->category_ids = $categoryIds;
            $esItem->configurable_options = null;
            $esItem->custom_options = $customOptions;
            $esItem->description = $description;
            $esItem->id = $id;
            $esItem->image = $image;
            $esItem->meta_description = $metaDescription;
            $esItem->meta_title = $metaTitle;
            $esItem->name = $name;
            $esItem->original_price_incl_tax = $originalPriceInclTax;
            $esItem->parentSku = $parentSku;
            $esItem->price_incl_tax = $priceInclTax;
            $esItem->product_links = [];
            $esItem->qty = $qty;
            $esItem->sku = $sku;
            $esItem->slug = $slug;
            $esItem->special_price = $specialPrice;
            $esItem->status = $status;
            $esItem->stock = [];
            $esItem->type_id = $typeId;
            $esItem->updated_at = $updatedAt;
            $esItem->url_path = $urlPath;
            $esItem->visibility = $visibility;

            $result[] = $esItem;
        }
        return $result;
    }

    /**
     * Clean up all data from product index in Elasticsearch before replication.
     */
    private function deleteEsData()
    {
        $where = '';
        $resp = $this->daoProd->deleteSet($where);
        $deleted = $resp['deleted'];
        $this->logger->info("Replication service deletes all products data in ElasticSearch ($deleted items).");
    }

    /**
     * Perform replication for all catalog products.
     * @param \Flancer32\VsfAdapter\Service\Replicate\Product\Request|null $request
     * @return \Flancer32\VsfAdapter\Service\Replicate\Product\Response
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(ARequest $request = null)
    {
        $result = new AResponse();
        $this->logger->info("Full replication for products is started.");
        $indexPrefix = $request->indexPrefix;
        $storeId = $request->storeId;

        /* set current store according to request */
        $storeIdCurrent = $this->mgrStore->getStore()->getId();
        $this->mgrStore->setCurrentStore($storeId);

        /* rebuild Elasticsearch client according to given store view */
        $this->adapterEs->rebuildClient();
        if ($indexPrefix) {
            /* reset index prefix if requested */
            $this->adapterEs->setIndexPrefix($indexPrefix);
        }
        $indexPrefix = $this->adapterEs->getIndexPrefix();
        $this->logger->info("Full replication for products is started (index: '$indexPrefix...'; store: $storeId).");

        /* get Magento data, convert it to ES form then index data  */
        $mageProds = $this->getMageProducts($storeId);
        $esProds = $this->convertMageToEs($mageProds);
        $total = count($esProds);
        $this->logger->info("Total '$total' products is found to be replicated.");

        /* remove all indexes from ES then create new ones */
        $this->deleteEsData();
        $this->saveEsData($esProds);
        $this->logger->info("Full replication for categories is completed.");

        /* restore current store */
        $this->mgrStore->setCurrentStore($storeIdCurrent);
        return $result;
    }

    /**
     * https://magento.stackexchange.com/questions/130185/how-to-get-a-list-of-all-products-in-magento-2
     *
     * @param int $storeId
     * @return \Magento\Catalog\Model\Product[]
     */
    private function getMageProducts($storeId)
    {
        $filter = $this->buildFilter
            ->setField(\Magento\Catalog\Model\Product::STORE_ID)
            ->setConditionType('eq')
            ->setValue($storeId)
            ->create();
        $this->buildCriteria->addFilters([$filter]);
//        $this->buildCriteria->setPageSize(10);
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $criteria */
        $criteria = $this->buildCriteria->create();
        $products = $this->repoProd->getList($criteria);
        /** @var \Magento\Catalog\Model\Product[] $result */
        $result = $products->getItems();
        $count = count($result);
        $this->logger->info("Total: $count.");
        return $result;
    }

    /**
     * @param \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product[] $esProds
     */
    private function saveEsData($esProds)
    {
        $created = $updated = 0;  // yes, all products should be saved, not updated
        foreach ($esProds as $one) {
            $resp = $this->daoProd->create($one);
            $id = $resp['_id'];
            $action = $resp['result'];  // saved|updated
            $name = $one->name;
            $sku = $one->sku;
            $this->logger->debug("Product #$id is $action ($sku: $name).");
            ($action == 'created') ? $created++ : $updated++;
        }
        $this->logger->info("'$created' items were created and '$updated' items were updated.");
    }
}
