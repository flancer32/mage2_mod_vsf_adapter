<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Controller\Adminhtml\Replicate\Inventory;

use Flancer32\VsfAdapter\Config as Cfg;

class Index
    extends \Magento\Backend\App\Action
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu(Cfg::MENU_REPLICATE_INVENTORY);
        $resultPage->getConfig()->getTitle()->prepend(__('Inventory Replication'));
        return $resultPage;
    }

}
