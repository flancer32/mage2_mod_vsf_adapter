<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Service\Replicate\Catalog\A;

use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Attr as EEsAttr;
use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category as EEsCat;
use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product as EEsProd;

/**
 * Save catalog data to Elasticsearch.
 */
class Save
{

    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Attr */
    private $daoAttr;
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Category */
    private $daoCat;
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Product */
    private $daoProd;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Attr $daoAttr,
        \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Category $daoCat,
        \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Product $daoProd
    ) {
        $this->logger = $logger;
        $this->daoAttr = $daoAttr;
        $this->daoCat = $daoCat;
        $this->daoProd = $daoProd;
    }

    private function cleanAttributes()
    {
        $where = '';
        $resp = $this->daoAttr->deleteSet($where);
        $deleted = $resp['deleted'];
        $this->logger->info("Attributes data is deleted from ElasticSearch ($deleted items).");
    }

    private function cleanCategories()
    {
        $where = '';
        $resp = $this->daoCat->deleteSet($where);
        $deleted = $resp['deleted'];
        $this->logger->info("Categories data is deleted from ElasticSearch ($deleted items).");
    }

    private function cleanProducts()
    {
        $where = '';
        $resp = $this->daoProd->deleteSet($where);
        $deleted = $resp['deleted'];
        $this->logger->info("Products data is deleted from ElasticSearch ($deleted items).");
    }

    /**
     * Save catalog data to Elasticsearch.
     *
     * @param EEsAttr[] $esAttrs
     * @param EEsCat[] $esCats
     * @param EEsProd[] $esProds
     */
    public function exec($esAttrs, $esCats, $esProds)
    {
        $this->cleanAttributes();
        $this->saveAttributes($esAttrs);
        $this->cleanCategories();
        $this->saveCategories($esCats);
        $this->cleanProducts();
        $this->saveProducts($esProds);
    }

    /**
     * @param EEsAttr[] $items
     */
    private function saveAttributes($items)
    {
        $created = $other = 0;
        foreach ($items as $one) {
            $resp = $this->daoAttr->create($one);
            $id = $resp['_id'];
            $action = $resp['result'];  // saved|updated
            $code = $one->attribute_code;
            $this->logger->debug("Attribute #$id is $action ($code).");
            ($action == 'created') ? $created++ : $other++;
        }
        $this->logger->info("'$created' attribute items were created ('$other' requests have other result).");
    }

    /**
     * @param EEsCat[] $items
     */
    private function saveCategories($items)
    {
        $created = $other = 0;
        foreach ($items as $one) {
            $resp = $this->daoCat->create($one);
            $id = $resp['_id'];
            $action = $resp['result'];  // saved|updated
            $code = $one->name;
            $this->logger->debug("Category #$id is $action ($code).");
            ($action == 'created') ? $created++ : $other++;
        }
        $this->logger->info("'$created' category items were created ('$other' requests have other result).");
    }

    /**
     * @param EEsProd[] $items
     */
    private function saveProducts($items)
    {
        $created = $other = 0;
        foreach ($items as $one) {
            $resp = $this->daoProd->create($one);
            $id = $resp['_id'];
            $action = $resp['result'];  // saved|updated
            $name = $one->name;
            $sku = $one->sku;
            $this->logger->debug("Product #$id is $action ($sku: $name).");
            ($action == 'created') ? $created++ : $other++;
        }
        $this->logger->info("'$created' product items were created ('$other' requests have other result).");
    }
}
