<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Catalog\A;

use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category as EEsCat;
use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product as EEsProd;
use Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Data\Attr as DAttr;

/**
 * Retrieve Magento data and prepare ES compatible data structures.
 */
class Load
{

    /** @var \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Attribute */
    private $aAttribute;
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Category */
    private $aCategory;
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Product */
    private $aProduct;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Attribute $aAttribute,
        \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Category $aCategory,
        \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load\A\Product $aProduct
    ) {
        $this->logger = $logger;
        $this->aAttribute = $aAttribute;
        $this->aCategory = $aCategory;
        $this->aProduct = $aProduct;
    }

    /**
     * Load Magento data and prepare ES compatible data structures.
     *
     * @param int $storeId
     * @return array
     */
    public function exec($storeId)
    {
        /** @var EEsCat[] $esCats */
        $esCats = $this->aCategory->exec();
        /** @var DAttr[] $attrsData */
        $attrsData = $this->aAttribute->exec($storeId);
        /** @var EEsProd[] $esProds */
        [$esProds, $esAttrs] = $this->aProduct->exec($storeId, $attrsData);

        return [$esAttrs, $esCats, $esProds];
    }
}
