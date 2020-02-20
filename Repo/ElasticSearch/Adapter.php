<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Repo\ElasticSearch;


class Adapter
{
    /** @var \Elasticsearch\Client */
    private $client;
    /** @var \Flancer32\VsfAdapter\Helper\Config */
    private $hlpConfig;
    /** @var string */
    private $indexPrefix;

    public function __construct(
        \Flancer32\VsfAdapter\Helper\Config $hlpConfig
    ) {
        $this->hlpConfig = $hlpConfig;
    }

    /**
     * @return \Elasticsearch\Client
     */
    public function getClient(): \Elasticsearch\Client
    {
        if (is_null($this->client)) {
            $this->rebuildClient();
        }
        return $this->client;
    }

    public function getIndexPrefix(): string
    {
        if (is_null($this->indexPrefix)) {
            $this->indexPrefix = $this->hlpConfig->getConfigEsIndexPrefix();
        }
        return $this->indexPrefix;
    }

    /**
     * Rebuild client after current store view changes.
     *
     * @return \Elasticsearch\Client
     */
    public function rebuildClient()
    {
        $host = $this->hlpConfig->getConfigEsHost();
        $scheme = $this->hlpConfig->getConfigEsScheme();
        $port = $this->hlpConfig->getConfigEsPort();
        $hostLocal = ['host' => $host, 'scheme' => $scheme, 'port' => $port];
        $this->client = \Elasticsearch\ClientBuilder::create()
            ->setHosts([$hostLocal])
            ->build();
        $this->indexPrefix = $this->hlpConfig->getConfigEsIndexPrefix();
    }

    /**
     * @param string $data
     */
    public function setIndexPrefix(string $data): void
    {
        $this->indexPrefix = $data;
    }

}
