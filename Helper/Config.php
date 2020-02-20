<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Helper;

use Magento\Store\Model\ScopeInterface as AScope;

class Config
{
    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfigEsHost()
    {
        return $this->scopeConfig->getValue('vsf_config/es/host', AScope::SCOPE_STORE);
    }

    public function getConfigEsIndexPrefix()
    {
        return $this->scopeConfig->getValue('vsf_config/es/index_prefix', AScope::SCOPE_STORE);
    }

    public function getConfigEsPort()
    {
        $result = $this->scopeConfig->getValue('vsf_config/es/port', AScope::SCOPE_STORE);
        return filter_var($result, FILTER_VALIDATE_INT);
    }

    public function getConfigEsScheme()
    {
        return $this->scopeConfig->getValue('vsf_config/es/scheme', AScope::SCOPE_STORE);
    }

}
