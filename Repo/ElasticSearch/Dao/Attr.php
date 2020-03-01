<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Repo\ElasticSearch\Dao;

use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Attr as DataEntity;

/**
 * Data access object to operate with data in Elasticsearch.
 */
class Attr
    extends \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter\Dao
{
    const ENTITY_CLASS = DataEntity::class;
    const ENTITY_NAME = 'attribute';
    const ENTITY_PK = 'attribute_id';

    public function getEntityClass()
    {
        return DataEntity::class;
    }
}
