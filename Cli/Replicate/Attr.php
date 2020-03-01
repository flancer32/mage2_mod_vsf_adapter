<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Cli\Replicate;

use Flancer32\VsfAdapter\Service\Replicate\Attr\Request as ARequest;

class Attr
    extends \Symfony\Component\Console\Command\Command
{
    const DESC = 'Replicate products attributes data from Magento to VSF.';
    const NAME = 'fl32:vsf:replicate:attr';
    const OPT_INDEX = 'index';
    const OPT_INDEX_SHORT = 'i';
    const OPT_STORE = 'store';
    const OPT_STORE_SHORT = 's';

    /** @var \Magento\Framework\App\State */
    private $appState;
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $manObj;
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Attr */
    private $srvReplicate;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Framework\App\State $appState,
        \Flancer32\VsfAdapter\Service\Replicate\Attr $srvReplicate
    ) {
        /* these objects are used in parent::__construct/configure */
        $this->manObj = $manObj;
        $this->appState = $appState;
        /* these objects are own props */
        $this->srvReplicate = $srvReplicate;
        parent::__construct(self::NAME);
        $this->initOptions();
        /* Symfony related config is performed from parent constructor */
        $this->setDescription(self::DESC);
    }

    /**
     * Sets area code to start an adminhtml session and configure Object Manager.
     */
    private function checkAreaCode()
    {
        try {
            /* area code should be set only once */
            $this->appState->getAreaCode();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            /* exception will be thrown if no area code is set */
            $areaCode = \Magento\Framework\App\Area::AREA_GLOBAL;
            $this->appState->setAreaCode($areaCode);
            /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
            $configLoader = $this->manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
            $config = $configLoader->load($areaCode);
            $this->manObj->configure($config);
        }
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $this->checkAreaCode();
        /* perform operation */
        $name = self::NAME;
        $output->writeln("Command '$name' is started.");
        $storeId = $input->getOption(self::OPT_STORE);
        $prefix = $input->getOption(self::OPT_INDEX);
        $output->writeln("Mage store view: '$storeId'; ES indexes prefix: '$prefix'.");
        $req = new ARequest();
        $req->indexPrefix = $prefix;
        $req->storeId = $storeId;
        $this->srvReplicate->execute($req);
        $output->writeln("Command '$name' is executed.");
    }

    private function initOptions()
    {
        $this->addOption(
            self::OPT_INDEX,
            self::OPT_INDEX_SHORT,
            \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED,
            'Prefix to construct names for ElasticSearch index (`vsf_msk_` => `vsf_msk_product`).'
        );
        $this->addOption(
            self::OPT_STORE,
            self::OPT_STORE_SHORT,
            \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED,
            'ID of the Magento store view to get localized data (russian, english, etc.).'
        );
    }
}
