<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate;

use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Attr as EAttr;
use Flancer32\VsfAdapter\Service\Replicate\Attr\Request as ARequest;
use Flancer32\VsfAdapter\Service\Replicate\Attr\Response as AResponse;

/**
 * Completely delete all product attributes data from ElasticSearch and index new ones.
 */
class Attr
{
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter */
    private $adapterEs;
    /** @var \Magento\Framework\Api\Search\SearchCriteriaInterfaceFactory */
    private $buildCriteria;
    /** @var \Magento\Framework\Api\FilterBuilder */
    private $buildFilter;
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Attr */
    private $daoAttr;
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
        \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Attr $daoAttr
    ) {
        $this->logger = $logger;
        $this->buildCriteria = $buildCriteria;
        $this->buildFilter = $buildFilter;
        $this->mgrStore = $mgrStore;
        $this->repoProd = $repoProd;
        $this->adapterEs = $adapterEs;
        $this->daoAttr = $daoAttr;
    }

    /**
     * Convert product data from Magento format to ElasticSearch format.
     *
     * @param \Magento\Catalog\Model\Product[] $mageAttrs
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product[]
     */
    private function convertMageToEs($mageAttrs)
    {
        $result = [];
        foreach ($mageAttrs as $one) {
            // prepare intermediate data
            $id = $one->getId();
            // prepare target data object
            $esItem = new EAttr();
            $esItem->id = $id;

            $result[] = $esItem;
        }
        return $result;
    }

    /**
     * Clean up all data from attribute index in Elasticsearch before replication.
     */
    private function deleteEsData()
    {
        $where = '';
        $resp = $this->daoAttr->deleteSet($where);
        $deleted = $resp['deleted'];
        $this->logger->info("Replication service deletes all products data in ElasticSearch ($deleted items).");
    }

    /**
     * Perform replication for all catalog products.
     * @param \Flancer32\VsfAdapter\Service\Replicate\Attr\Request|null $request
     * @return \Flancer32\VsfAdapter\Service\Replicate\Attr\Response
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
//        $mageProds = $this->getMageProducts($storeId);
//        $esProds = $this->convertMageToEs($mageProds);
//        $total = count($esProds);
//        $this->logger->info("Total '$total' products is found to be replicated.");
//
        /* remove all indexes from ES then create new ones */
        $this->deleteEsData();
//        $this->saveEsData($esProds);
//        $this->logger->info("Full replication for categories is completed.");

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
            $resp = $this->daoAttr->create($one);
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
