<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Model\Config\Source;

/**
 * Select options source for store views.
 */
class StoreViews
    implements \Magento\Framework\Data\OptionSourceInterface
{
    private $options;
    /** @var \Magento\Store\Api\StoreRepositoryInterface */
    private $repoStore;

    public function __construct(
        \Magento\Store\Api\StoreRepositoryInterface $repoStore
    ) {
        $this->repoStore = $repoStore;
    }

    public function toOptionArray()
    {
        if (is_null($this->options)) {
            $options = [];
            $stores = $this->repoStore->getList();
            foreach ($stores as $store) {
                $id = $store->getId();
                if ($id != \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
                    // process all stores except admin
                    $name = $store->getName();
                    $options[] = ['label' => __($name), 'value' => $id];
                }
            }
            $this->options = $options;
        }
        return $this->options;
    }
}
