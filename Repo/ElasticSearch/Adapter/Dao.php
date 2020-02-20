<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter;

/**
 * Base implementation for entity's DAO.
 */
abstract class Dao
    implements IDao
{
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter */
    private $adapter;

    public function __construct(
        \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter $adapter
    ) {
        $this->adapter = $adapter;
    }

    public function create($data)
    {
        $index = $this->getIndexName();
        $pkey = $this->getPrimaryKey();
        $id = $data->$pkey;
        $params = [
            'index' => $index,
            'id' => $id,
            'type' => '_doc',
            'body' => $data
        ];
        $client = $this->getEsClient();
        return $client->index($params);
    }

    public function deleteSet($where)
    {
        $index = $this->getIndexName();
        $query = ['match_all' => new\stdClass()];
        $params = [
            'index' => $index,
            'body' => ['query' => $query]
        ];
        $client = $this->getEsClient();
        return $client->deleteByQuery($params);
    }

    private function getEntityName()
    {
        return static::ENTITY_NAME;
    }

    /**
     * @return \Elasticsearch\Client
     */
    private function getEsClient()
    {
        return $this->adapter->getClient();
    }

    private function getIndexName()
    {
        return $this->adapter->getIndexPrefix() . $this->getEntityName();
    }

    public function getPrimaryKey()
    {
        return static::ENTITY_PK;
    }
}
