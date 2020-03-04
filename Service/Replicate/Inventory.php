<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate;

use Flancer32\VsfAdapter\Service\Replicate\Inventory\Request as ARequest;
use Flancer32\VsfAdapter\Service\Replicate\Inventory\Response as AResponse;

/**
 * Add inventory data (prices & stock) from Magento to products index in Elasticsearch.
 */
class Inventory
{
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter */
    private $adapterEs;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $mgrStore;

    public function __construct(
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Magento\Store\Model\StoreManagerInterface $mgrStore,
        \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter $adapterEs
    ) {
        $this->logger = $logger;
        $this->mgrStore = $mgrStore;
        $this->adapterEs = $adapterEs;
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
     * @param \Flancer32\VsfAdapter\Service\Replicate\Inventory\Request|null $request
     * @return \Flancer32\VsfAdapter\Service\Replicate\Inventory\Response
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(ARequest $request = null)
    {
        $result = new AResponse();
        $this->logger->info("Inventory replication is started.");
        $indexPrefix = $request->indexPrefix;
        $storeId = $request->storeId;

        /* set current store according to request */
        $storeIdCurrent = $this->mgrStore->getStore()->getId();
        $this->mgrStore->setCurrentStore($storeId);
        $storeId = $this->mgrStore->getStore()->getId();

        /* setup Elasticsearch adapter */
        $indexPrefix = $this->adapterSetup($indexPrefix);
        $this->logger->info("Inventory replication parameters: index '$indexPrefix', store $storeId.");

        /* perform data replication itself */

        /* restore current store */
        $this->mgrStore->setCurrentStore($storeIdCurrent);
        $this->logger->info("Inventory replication is completed.");
        $result->success = true;
        return $result;
    }

}
