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
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Z\QtyLoad */
    private $qtyLoad;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProd;

    public function __construct(
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Magento\Framework\Api\SearchCriteriaBuilder $buildCriteria,
        \Magento\Framework\Api\FilterBuilder $buildFilter,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd,
        \Flancer32\VsfAdapter\Service\Replicate\Z\Helper\Convert $convert,
        \Flancer32\VsfAdapter\Service\Replicate\Z\QtyLoad $qtyLoad
    ) {
        $this->logger = $logger;
        $this->buildCriteria = $buildCriteria;
        $this->buildFilter = $buildFilter;
        $this->repoProd = $repoProd;
        $this->convert = $convert;
        $this->qtyLoad = $qtyLoad;
    }

    /**
     * Convert products data from Magento format to ElasticSearch format.
     *
     * @param \Magento\Catalog\Model\Product[] $mageProds
     * @param array $inventory [SKU => QTY]
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product[]
     */
    private function convertMageToEs($mageProds, $inventory)
    {
        $result = [];
        foreach ($mageProds as $one) {
            // get inventory data if exists
            $stock = null;
            $prodId = $one->getId();
            if (isset($inventory[$prodId])) {
                $stock = $inventory[$prodId];
            }
            // create ES data item for product with base attributes
            $esItem = $this->convert->productDataToEs($one, $stock);
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
        $inventory = $this->qtyLoad->exec($storeId);
        $result = $this->convertMageToEs($mageProds, $inventory);
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
