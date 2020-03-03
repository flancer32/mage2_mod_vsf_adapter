<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Block\Adminhtml\Catalog\Replicate;

use Flancer32\VsfAdapter\Service\Replicate\Catalog\Request as ARequest;

class Report
    extends \Magento\Backend\Block\Template
{
    /** @see view/adminhtml/ui_component/fl32vsf_catalog_replicate_form.xml */
    const FIELDSET = 'catalog_replicate_form';
    const FIELD_STORE_VIEW = 'store_view';

    /** @var \Flancer32\VsfAdapter\App\Logger */
    private $logger;
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Catalog */
    private $srvReplicate;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Flancer32\VsfAdapter\App\Logger $logger,
        \Flancer32\VsfAdapter\Service\Replicate\Catalog $srvReplicate,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->logger = $logger;
        $this->srvReplicate = $srvReplicate;
    }

    protected function _beforeToHtml()
    {
        /* Parse posted HTTP data & load report data from DB */
        $req = $this->getRequest();
        $params = $req->getParam(self::FIELDSET);
        $storeId = $params[self::FIELD_STORE_VIEW];

        /* Set up logging level for in-memory handler */
        $this->logger->getHandlerMemory()->setLevel(\Monolog\Logger::INFO);

        /* perform service call */
        try {
            $req = new ARequest();
            $req->storeId = $storeId;
            $this->srvReplicate->execute($req);
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
