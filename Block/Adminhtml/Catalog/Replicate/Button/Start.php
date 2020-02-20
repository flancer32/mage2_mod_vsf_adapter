<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Block\Adminhtml\Catalog\Replicate\Button;

class Start
    implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    public function getButtonData()
    {
        $data = [
            'label' => __('Start'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ]
        ];
        return $data;
    }
}
