<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Repo\ElasticSearch\Dao;


use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category as DataEntity;

class Category
    extends \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter\Dao
{
    const ENTITY_CLASS = DataEntity::class;
    const ENTITY_NAME = 'category';
    const ENTITY_PK = 'id';

    public function deleteOne($pk)
    {
        // TODO: Implement deleteOne() method.
    }

    public function getAttributes(): array
    {
        // TODO: Implement getAttributes() method.
    }

    public function getEntityClass()
    {
        // TODO: Implement getEntityClass() method.
    }

    public function getEntityPath()
    {
        // TODO: Implement getEntityPath() method.
    }

    public function getOne($key)
    {
        // TODO: Implement getOne() method.
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
