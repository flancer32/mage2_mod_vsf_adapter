<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Inventory\A\Load;


class Mage
{
    /** @var \Magento\Framework\Api\Search\SearchCriteriaInterfaceFactory */
    private $buildCriteria;
    /** @var \Magento\Framework\Api\FilterBuilder */
    private $buildFilter;
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Z\Helper\Convert */
    private $convert;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProd;

    public function __construct(
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Magento\Framework\Api\SearchCriteriaBuilder $buildCriteria,
        \Magento\Framework\Api\FilterBuilder $buildFilter,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd,
        \Flancer32\VsfAdapter\Service\Replicate\Z\Helper\Convert $convert
    ) {
        $this->logger = $logger;
        $this->buildCriteria = $buildCriteria;
        $this->buildFilter = $buildFilter;
        $this->repoProd = $repoProd;
        $this->convert = $convert;
    }

    /**
     * Convert products data from Magento format to ElasticSearch format.
     *
     * @param \Magento\Catalog\Model\Product[] $mageProds
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product[]
     */
    private function convertMageToEs($mageProds)
    {
        $result = [];
        foreach ($mageProds as $one) {
            // create ES data item for product with base attributes
            $esItem = $this->convert->productDataToEs($one);
            $id = $esItem->id;
            $result[$id] = $esItem;
        }
        return $result;
    }

    public function exec($storeId)
    {
        $mageProds = $this->getMageProducts($storeId);
        $total = count($mageProds);
        $this->logger->info("Total '$total' product items were loaded from Magento.");
        $result = $this->convertMageToEs($mageProds);
        $totalProds = count($result);
        $this->logger->info("Total '$totalProds' products were converted to Elasticsearch compatible format.");
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
//        $this->buildCriteria->setPageSize(10); // limit result set items in dev. purposes
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $criteria */
        $criteria = $this->buildCriteria->create();
        $products = $this->repoProd->getList($criteria);
        /** @var \Magento\Catalog\Model\Product[] $result */
        $result = $products->getItems();
        return $result;
    }
}
