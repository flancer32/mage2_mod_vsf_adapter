<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate;


use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category as ECategory;
use Flancer32\VsfAdapter\Service\Replicate\Category\Request as ARequest;
use Flancer32\VsfAdapter\Service\Replicate\Category\Response as AResponse;

/**
 * Completely delete all categories data from ElasticSearch and index new ones.
 */
class Category
{
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter */
    private $adapterEs;
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Category\A\Indexer */
    private $anIndexer;
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Category */
    private $daoCat;
    /** @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory */
    private $factCategoryCollection;
    /** @var \Magento\Catalog\Helper\Category */
    private $hlpCatalogCategory;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $mgrStore;

    public function __construct(
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Magento\Store\Model\StoreManagerInterface $mgrStore,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $factCategoryCollection,
        \Magento\Catalog\Helper\Category $hlpCatalogCategory,
        \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter $adapterEs,
        \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Category $daoCat,
        \Flancer32\VsfAdapter\Service\Replicate\Category\A\Indexer $anIndexer
    ) {
        $this->logger = $logger;
        $this->mgrStore = $mgrStore;
        $this->factCategoryCollection = $factCategoryCollection;
        $this->hlpCatalogCategory = $hlpCatalogCategory;
        $this->adapterEs = $adapterEs;
        $this->daoCat = $daoCat;
        $this->anIndexer = $anIndexer;
    }

    /**
     * Convert category data from Magento format to ElasticSearch format (w/o hierarchical data in `children_data`).
     *
     * @param \Magento\Catalog\Model\Category[] $mageCats
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category[]
     */
    private function convertMageToEs($mageCats)
    {
        $result = [];
        foreach ($mageCats as $one) {
            // prepare intermediate data
            $id = $one->getId();
            $slug = "--$id";
            $urlKey = $slug;
            $urlPath = $one->getUrlPath() . "/$urlKey";
            // prepare target data object
            $esItem = new ECategory();
            $esItem->children = [];
            $esItem->children_count = 0;
            $esItem->children_data = [];
            $esItem->id = (int)$one->getId();
            $esItem->is_active = (bool)$one->getIsActive();
            $esItem->level = (int)$one->getLevel();
            $esItem->name = (string)$one->getName();
            $esItem->parent_id = (int)$one->getParentId();
            $esItem->path = (string)$one->getPath();
            $esItem->position = (int)$one->getPosition();
            $esItem->product_count = (int)$one->getProductCount();
            $esItem->slug = $slug;
            $esItem->url_key = $urlKey;
            $esItem->url_path = $urlPath;
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
        $resp = $this->daoCat->deleteSet($where);
        $deleted = $resp['deleted'];
        $this->logger->info("Replication service deletes all category data in ElasticSearch ($deleted items).");
    }

    /**
     * Perform replication for all catalog categories.
     * @param \Flancer32\VsfAdapter\Service\Replicate\Category\Request|null $request
     * @return \Flancer32\VsfAdapter\Service\Replicate\Category\Response
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(ARequest $request = null)
    {
        $result = new AResponse();
        $this->logger->info("Full replication for categories is started.");
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
        $this->logger->info("Full replication for categories is started (index: '$indexPrefix...'; store: $storeId).");

        /* get Magento data, convert it to ES form then index data  */
        $mageCats = $this->getMageCategories($storeId);
        $esCats = $this->convertMageToEs($mageCats);
        $esCats = $this->anIndexer->exec($esCats);
        $total = count($esCats);
        $this->logger->info("Total '$total' categories is found to be replicated.");

        /* remove all indexes from ES then create new ones */
        $this->deleteEsData();
        $this->saveEsData($esCats);
        $this->logger->info("Full replication for categories is completed.");

        /* restore current store */
        $this->mgrStore->setCurrentStore($storeIdCurrent);
        return $result;
    }

    private function getMageCategories()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->hlpCatalogCategory->getStoreCategories(false, true, false);
        $collection->addAttributeToSelect('*');
        return $collection->getItems();
    }

    /**
     * @param \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category[] $esCats
     */
    private function saveEsData($esCats)
    {
        foreach ($esCats as $one) {
            $resp = $this->daoCat->create($one);
            $id = $resp['_id'];
            $action = $resp['result'];  // saved|updated
            $name = $one->name;
            $this->logger->debug("Category #$id is $action ($name).");
        }
    }
}