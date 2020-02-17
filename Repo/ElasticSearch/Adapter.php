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
    /** @var string */
    private $indexPrefix;

    public function __construct()
    {
    }

    /**
     * @return \Elasticsearch\Client
     */
    public function getClient(): \Elasticsearch\Client
    {
        if (is_null($this->client)) {
            $host = 'localhost';
            $scheme = 'http';
            $port = '9200';
            $hostLocal = ['host' => $host, 'scheme' => $scheme, 'port' => $port];
            $this->client = \Elasticsearch\ClientBuilder::create()
                ->setHosts([$hostLocal])
                ->build();
        }
        return $this->client;
    }

    public function getIndexPrefix(): string
    {
        return $this->indexPrefix;
    }

    /**
     * @param string $data
     */
    public function setIndexPrefix(string $data): void
    {
        $this->indexPrefix = $data;
    }

}
