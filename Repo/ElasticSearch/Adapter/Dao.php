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
    private const TYPE = '_doc';
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
            'type' => self::TYPE,
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

    public abstract function getEntityClass();

    public function getEntityEmpty()
    {
        $class = $this->getEntityClass();
        return new $class();
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
        return $this->adapter->getIndexPrefix() . '_' . $this->getEntityName();
    }

    public function getOne($key)
    {
        $result = null;
        /* Elasticsearch has simple string identifiers */
        $index = $this->getIndexName();
        $params = [
            'index' => $index,
            'type' => self::TYPE,
            'id' => $key
        ];
        $client = $this->getEsClient();
        $resp = $client->get($params);
        if (isset($resp['found']) && $resp['found']) {
            $source = $resp['_source'];
            $result = $this->parseSource($source);
        }
        return $result;
    }

    public function getPrimaryKey()
    {
        return static::ENTITY_PK;
    }

    public function getSet($where = null, $bind = null, $order = null, $limit = null, $offset = null)
    {
        // get all entities for beginning
        $result = [];
        /* Elasticsearch has simple string identifiers */
        $index = $this->getIndexName();
        $params = [
            'index' => $index,
            'type' => self::TYPE
        ];
        if ($limit) {
            $params['size'] = (int)$limit;
        }
        if ($offset) {
            $params['from'] = (int)$offset;
        }
        // searching query
        $params['body'] = [
            'query' => [
                'match_all' => new \stdClass()
            ]
        ];
        $client = $this->getEsClient();
        $resp = $client->search($params);
        if (
            isset($resp['hits']) &&
            isset($resp['hits']['hits']) &&
            is_array($resp['hits']['hits'])
        ) {
            $hits = $resp['hits']['hits'];
            foreach ($hits as $hit) {
                $id = $hit['_id'];
                $source = $hit['_source'];
                $item = $this->parseSource($source);
                $result[$id] = $item;
            }
        }
        return $result;
    }

    /**
     * Parse Elasticsearch source data and create new data object.
     *
     * @param array $source
     * @return DataEntity[]
     */
    private function parseSource($source)
    {
        $class = $this->getEntityClass();
        $result = new $class();
        foreach ($source as $key => $value) {
            // transfer attributes for one level only (w/o recursion)
            // TODO: add recursion with "$this->getEntityEmpty()"
            $result->$key = $value;
        }
        return $result;
    }
}
