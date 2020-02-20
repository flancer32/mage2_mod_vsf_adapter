<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Block\Adminhtml\Catalog\Replicate;

use Flancer32\VsfAdapter\Service\Replicate\Category\Full\Request as ARequest;
use Flancer32\VsfAdapter\Service\Replicate\Category\Full\Response as AResponse;

class Report
    extends \Magento\Backend\Block\Template
{
    const FIELDSET = 'catalog_replicate_form';
    const FIELD_STORE_VIEW = 'store_view';

    /** @var \Flancer32\VsfAdapter\App\Logger */
    private $logger;
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Category\Full */
    private $srvReplicateCat;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Flancer32\VsfAdapter\Service\Replicate\Category\Full $srvReplicateCat,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->logger = $logger;
        $this->srvReplicateCat = $srvReplicateCat;
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
            $req = new ARequest();
            $req->storeId = $storeId;
            /** @var AResponse $resp */
            $resp = $this->srvReplicateCat->execute($req);
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
