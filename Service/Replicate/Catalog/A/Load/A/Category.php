<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A;

/**
 * Get categories data from Magento and convert it to Elasticsearch format.
 */
class Category
{
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Category\A\Convert */
    private $aConvert;
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Category\A\Indexer */
    private $aIndexer;
    /** @var \Magento\Catalog\Helper\Category */
    private $hlpCatalogCategory;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Magento\Catalog\Helper\Category $hlpCatalogCategory,
        \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Category\A\Convert $aConvert,
        \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Category\A\Indexer $aIndexer
    ) {
        $this->logger = $logger;
        $this->hlpCatalogCategory = $hlpCatalogCategory;
        $this->aConvert = $aConvert;
        $this->aIndexer = $aIndexer;
    }

    /**
     * Convert Magento data into Elasticsearch data.
     *
     * @param \Magento\Catalog\Model\Category[] $mageItems
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category[]
     */
    private function convertToEsData($mageItems)
    {
        $result = [];
        foreach ($mageItems as $mageItem) {
            $esItem = $this->aConvert->mapMageDataToEs($mageItem);
            $result[] = $esItem;
        }
        return $result;
    }

    /**
     * Get categories data from Magento and convert it to Elasticsearch format.
     *
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category[]
     */
    public function exec()
    {
        $mageItems = $this->getMageCategories();
        $result = $this->convertToEsData($mageItems);
        // populate children data with hierarchical information (categories tree)
        $result = $this->aIndexer->exec($result);
        $total = count($result);
        $this->logger->info("Total '$total' category items were loaded from Magento.");
        return $result;
    }

    /**
     * @return \Magento\Catalog\Model\Category[]
     */
    private function getMageCategories()
    {
        $result = [];
        try {
            /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
            $collection = $this->hlpCatalogCategory->getStoreCategories(false, true, false);
            $collection->addAttributeToSelect('*');
            $result = $collection->getItems();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->error($e->getMessage());
        }
        return $result;
    }
}
