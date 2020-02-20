<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Model\Config\Source;

class HttpScheme
    implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [['value' => 'http', 'label' => 'HTTP'], ['value' => 'https', 'label' => 'HTTPS']];
    }
}
