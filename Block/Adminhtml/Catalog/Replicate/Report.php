<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Block\Adminhtml\Catalog\Replicate;

use Flancer32\VsfAdapter\Service\Replicate\Category\Request as ARequestCat;
use Flancer32\VsfAdapter\Service\Replicate\Category\Response as AResponseCat;
use Flancer32\VsfAdapter\Service\Replicate\Product\Request as ARequestProd;
use Flancer32\VsfAdapter\Service\Replicate\Product\Response as AResponseProd;

class Report
    extends \Magento\Backend\Block\Template
{
    /** @see view/adminhtml/ui_component/fl32vsf_catalog_replicate_form.xml */
    const FIELDSET = 'catalog_replicate_form';
    const FIELD_STORE_VIEW = 'store_view';

    /** @var \Flancer32\VsfAdapter\App\Logger */
    private $logger;
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Category */
    private $srvReplicateCat;
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Product */
    private $srvReplicateProd;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Flancer32\VsfAdapter\Service\Replicate\Category $srvReplicateCat,
        \Flancer32\VsfAdapter\Service\Replicate\Product $srvReplicateProd,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->logger = $logger;
        $this->srvReplicateCat = $srvReplicateCat;
        $this->srvReplicateProd = $srvReplicateProd;
    }

    protected function _beforeToHtml()
    {
        /* Parse posted HTTP data & load report data from DB */
        $req = $this->getRequest();
        $params = $req->getParam(self::FIELDSET);
        $storeId = $params[self::FIELD_STORE_VIEW];

        /* Set up logging level for in-momory handler */
        $this->logger->getHandlerMemory()->setLevel(\Monolog\Logger::INFO);

        /* perform service call */
        try {
            $req = new ARequestCat();
            $req->storeId = $storeId;
            /** @var AResponseCat $resp */
            $resp = $this->srvReplicateCat->execute($req);
            $req = new ARequestProd();
            $req->storeId = $storeId;
            /** @var AResponseProd $resp */
            $resp = $this->srvReplicateProd->execute($req);
        } catch (\Throwable $e) {
            $this->logger->err($e->getMessage());
        }
        return parent::_beforeToHtml();
    }

    /**
     * Return log messages from in-memory handler.
     *
     * @return string
     */
    public function outLog()
    {
        $hndl = $this->logger->getHandlerMemory();
        $stream = $hndl->getStream();
        rewind($stream);
        $result = stream_get_contents($stream);
        return $result;
    }
}
