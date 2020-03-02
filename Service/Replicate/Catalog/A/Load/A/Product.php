<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A;

use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Attr\Option as EAttrOption;
use Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Data\Attr as DAttr;

/**
 * Get products data from Magento, analyze its attributes then convert products & attributes data to
 * Elasticsearch format.
 */
class Product
{
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Product\A\Convert */
    private $aConvert;
    /** @var \Magento\Framework\Api\Search\SearchCriteriaInterfaceFactory */
    private $buildCriteria;
    /** @var \Magento\Framework\Api\FilterBuilder */
    private $buildFilter;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProd;

    public function __construct(
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Magento\Framework\Api\SearchCriteriaBuilder $buildCriteria,
        \Magento\Framework\Api\FilterBuilder $buildFilter,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd,
        \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Product\A\Convert $aConvert

    ) {
        $this->logger = $logger;
        $this->buildCriteria = $buildCriteria;
        $this->buildFilter = $buildFilter;
        $this->repoProd = $repoProd;
        $this->aConvert = $aConvert;
    }

    /**
     * Convert products & attributes data from Magento/intermediary format to ElasticSearch format.
     *
     * @param \Magento\Catalog\Model\Product[] $mageProds
     * @param \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Data\Attr[] $attrsData
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product[]
     */
    private function convertMageToEs($mageProds, $attrsData)
    {
        $esProds = [];
        $attrsOptions = []; // [$attrId][$optionId] = $value
        $mapAttrByCode = $this->mapAttrsByCode($attrsData);
        foreach ($mageProds as $one) {
            // create ES data item for product with base attributes
            $esItem = $this->aConvert->mapProductDataToEs($one);

            // register values for user defined attributes for current product
            foreach ($one->getData() as $attrCode => $optionId) {
                if (isset($mapAttrByCode[$attrCode])) {
                    $attrId = $mapAttrByCode[$attrCode];
                    if (!isset($attrsOptions[$attrId])) {
                        $attrsOptions[$attrId] = [];
                    }
                    $attr = $attrsData[$attrId];
                    $options = $attr->options;
                    if (
                        is_array($options) &&
                        !is_array($optionId) &&
                        isset($options[$optionId])
                    ) {
                        $option = $options[$optionId];
                        $attrsOptions[$attrId][$optionId] = $option->value;
                    }

                    // add user defined attribute to ES product
                    $esItem->$attrCode = $one->getData($attrCode);
                }
            }

            $esProds[] = $esItem;
        }

        // convert attributes and used options
        $esAttrs = $this->convertMageToEsAttrs($attrsData, $attrsOptions);

        return [$esProds, $esAttrs];
    }

    /**
     * @param \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Data\Attr[] $attrsData
     * @param array $attrRegistry options values for attributes being used in replicated products
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product[]
     */
    private function convertMageToEsAttrs($attrsData, $attrRegistry)
    {
        $result = [];
        foreach ($attrRegistry as $attrId => $options) {
            $attr = $attrsData[$attrId];
            $esAttr = $this->aConvert->mapAttrDataToEs($attr);

            if (is_array($options) && count($options)) {
                asort($options);
                $esAttr->options = [];
                foreach ($options as $optionId => $optionValue) {
                    $esOption = new EAttrOption();
                    $esOption->value = (int)$optionId;
                    $esOption->label = (string)$optionValue;
                    $esAttr->options[] = $esOption;
                }
            } else {
                unset($esAttr->options);
            }
            $result[] = $esAttr;
        }
        return $result;
    }

    /**
     * @param int $storeId
     * @param \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Data\Attr[] $attrData
     * @return array
     */
    public function exec($storeId, $attrData)
    {
        $mageProds = $this->getMageProducts($storeId);
        $total = count($mageProds);
        $this->logger->info("Total '$total' product items were loaded from Magento.");
        [$esProds, $esAttrs] = $this->convertMageToEs($mageProds, $attrData);
        $totalProds = count($esProds);
        $totalAttrs = count($esAttrs);
        $this->logger->info("Total '$totalProds' products & '$totalAttrs' attributes were converted to Elasticsearch compatible format.");
        return [$esProds, $esAttrs];
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

    /**
     * Create map [attrCode => attrId] to find attribute ID by attribute code.
     *
     * @param DAttr[] $attrs
     * @return array
     */
    private function mapAttrsByCode($attrs)
    {
        $result = [];
        foreach ($attrs as $attr) {
            $result[$attr->code] = $attr->id;
        }
        return $result;
    }
}
