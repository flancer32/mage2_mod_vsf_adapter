<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Flancer32\VsfAdapter\Repo\ElasticSearch\Dao;


use Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Category as DataEntity;

class Product
    extends \Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter\Dao
{
    const ENTITY_CLASS = DataEntity::class;
    const ENTITY_NAME = 'product';
    const ENTITY_PK = 'id';
}
