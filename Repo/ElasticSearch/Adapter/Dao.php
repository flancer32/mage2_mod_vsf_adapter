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

    public function deleteOne($pk)
    {
        // TODO: Implement deleteOne() method.
    }

    public function deleteSet($where)
    {
        $index = $this->getIndexName();
        $query = ['match_all' => new\stdClass()];
        $params = [
            'index' => $index,
            'body' => ['query' => $query]
        ];
        $res = $this->adapter->allowDelete($index);
        $client = $this->getEsClient();
        return $client->deleteByQuery($params);
    }

    public function getAttributes(): array
    {
        // TODO: Implement getAttributes() method.
    }

    public abstract function getEntityClass();

    private function getEntityName()
    {
        return static::ENTITY_NAME;
    }

    public function getEntityPath()
    {
        // TODO: Implement getEntityPath() method.
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

    public function getOne($key)
    {
        // TODO: Implement getOne() method.
    }

    public function getPrimaryKey()
    {
        return static::ENTITY_PK;
    }

    public function getSet($where = null, $bind = null, $order = null, $limit = null, $offset = null)
    {
        // TODO: Implement getSet() method.
    }

    public function updateOne($data)
    {
        // TODO: Implement updateOne() method.
    }

    public function updateSet($data, $where)
    {
        // TODO: Implement updateSet() method.
    }
}
