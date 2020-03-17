<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Inventory\A;

/**
 * Compare ES products with Magento products and update products in Elasticsearch.
 */
class Update
{
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Product */
    private $daoProd;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Product $daoProd
    ) {
        $this->logger = $logger;
        $this->daoProd = $daoProd;
    }

    /**
     * @param \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product[] $oldProds this array is updated by this method
     * @param \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product[] $newProds
     * @return array [$disabled, $updated, $noops]
     */
    public function exec(&$oldProds, $newProds)
    {
        $disabled = $this->refreshItems($oldProds, $newProds);
        $this->logger->info("'$disabled' products were disabled.");
        [$updated, $noops] = $this->saveUpdates($oldProds);
        return [$disabled, $updated, $noops];
    }

    /**
     * Scan all ES items and update repository related fields.
     *
     * @param \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product[] $oldProds this array is updated by this method
     * @param \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product[] $newProds
     * @return \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product[]
     */
    private function refreshItems(&$oldProds, $newProds)
    {
        $disabled = 0;
        // scan all ES items and get the same Mage item by ID
        foreach ($oldProds as $esProd) {
            $id = $esProd->id;
            if (isset($newProds[$id])) {
                $mageProd = $newProds[$id];
                $esProd->original_price_incl_tax = $mageProd->original_price_incl_tax;
                $esProd->price = $mageProd->price;
                $esProd->price_incl_tax = $mageProd->price_incl_tax;
                $esProd->special_price = $mageProd->special_price;
                $esProd->stock = $mageProd->stock;
            } else {
                // don't remove ES product, just mark as disabled (use catalog replication to remove extra items)
                $esProd->status = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
            }
        }
        return $disabled;
    }

    /**
     * @param \Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product[] $items
     * @return array [$updated, $noops]
     */
    private function saveUpdates($items)
    {
        $updated = $noops = $other = 0;
        foreach ($items as $one) {
            $resp = $this->daoProd->updateOne($one);
            $id = $resp['_id'];
            $action = $resp['result'];  // saved|updated
            $name = $one->name;
            $sku = $one->sku;
            $this->logger->debug("Product #$id is $action ($sku: $name).");
            if ($action == 'updated') {
                $updated++;
            } elseif ($action == 'noop') {
                $noops++;
            } else {
                $other++;
            }
        }
        $this->logger->info("'$updated' product items were updated and '$noops' were unchanged ('$other' requests have other result).");
        return [$updated, $noops];
    }
}
