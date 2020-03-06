<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate;

use Flancer32\VsfAdapter\Service\Replicate\Catalog\Request as ARequest;
use Flancer32\VsfAdapter\Service\Replicate\Catalog\Response as AResponse;

/**
 * Completely delete all catalog data from ElasticSearch and index new ones.
 */
class Catalog
{
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load */
    private $aLoad;
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Save */
    private $aSave;
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter */
    private $adapterEs;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $mgrStore;

    public function __construct(
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Magento\Store\Model\StoreManagerInterface $mgrStore,
        \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter $adapterEs,
        \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Load $aLoad,
        \Flancer32\VsfAdapter\Service\Replicate\Catalog\A\Save $aSave
    ) {
        $this->logger = $logger;
        $this->mgrStore = $mgrStore;
        $this->adapterEs = $adapterEs;
        $this->aLoad = $aLoad;
        $this->aSave = $aSave;
    }

    /**
     * Configure Elasticsearch adapter to use given prefix for indexes or get index prefix from configuration.
     *
     * @param string $indexPrefix
     * @return string
     */
    private function adapterSetup($indexPrefix)
    {
        /* rebuild Elasticsearch client according to current store view */
        $this->adapterEs->rebuildClient();
        if ($indexPrefix) {
            /* reset index prefix if requested */
            $this->adapterEs->setIndexPrefix($indexPrefix);
        }
        return $this->adapterEs->getIndexPrefix();
    }

    /**
     * Completely delete all catalog data from ElasticSearch and index new ones.
     *
     * @param \Flancer32\VsfAdapter\Service\Replicate\Catalog\Request|null $request
     * @return \Flancer32\VsfAdapter\Service\Replicate\Catalog\Response
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(ARequest $request = null)
    {
        $result = new AResponse();
        $this->logger->info("Catalog replication is started.");
        $indexPrefix = $request->indexPrefix;
        $storeId = $request->storeId;

        /* set current store according to request */
        $storeIdCurrent = $this->mgrStore->getStore()->getId();
        $this->mgrStore->setCurrentStore($storeId);
        $storeId = $this->mgrStore->getStore()->getId();

        /* setup Elasticsearch adapter */
        $indexPrefix = $this->adapterSetup($indexPrefix);
        $this->logger->info("Catalog replication parameters: index '$indexPrefix', store $storeId.");

        /* perform data replication itself */
        [$esAttrs, $esCats, $esProds] = $this->aLoad->exec($storeId);
        $this->aSave->exec($esAttrs, $esCats, $esProds);

        /* restore current store */
        $this->mgrStore->setCurrentStore($storeIdCurrent);
        $this->logger->info("Catalog replication is completed.");
        $result->attributes = count($esAttrs);
        $result->categories = count($esCats);
        $result->products = count($esProds);
        $result->success = true;
        return $result;
    }

}
