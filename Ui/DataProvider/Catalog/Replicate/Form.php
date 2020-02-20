<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Ui\DataProvider\Catalog\Replicate;

/**
 * Wrapper for UI data provider.
 */
class Form
    extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    public function __construct(
        string $name,
        string $primaryFieldName = 'id',
        string $requestFieldName = 'id',
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return null;
    }

    public function getData()
    {
        return [null => ['field' => 'value']];
    }
}
