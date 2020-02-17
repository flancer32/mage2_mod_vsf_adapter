<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Flancer32\VsfAdapter\Repo\ElasticSearch\Adapter;

/**
 * Base interface for entity's DAO.
 *
 * Descendants should override methods and define used data types.
 *
 * ATTN: 'DataEntity' is a fake type to be used in this interface.
 */
interface IDao
{
    /**
     * All these constants should be defined in descendants.
     * The constants are used in the base implementation of this interface.
     *
     * const ENTITY_CLASS = '\Vendor\Module\Api\Repo\Data\Entity'; // absolute classname for related Entity
     * const ENTITY_PK = ['key1', 'key2'];   // array with primary key attributes
     * const ENTITY_NAME = 'vnd_mod_entity'; // table name
     */

    /**
     * Create new entity.
     *
     * @param DataEntity $data
     * @return mixed
     */
    public function create($data);

    /**
     * Delete one entity using primary key (or entity itself - PK will be extracted).
     *
     * @param DataEntity|array|int|string $pk
     * @return int
     */
    public function deleteOne($pk);

    /**
     * Delete set of entities using $where condition.
     *
     * @param $where
     * @return mixed
     */
    public function deleteSet($where);

    /**
     * Get permanent attributes names.
     *
     * @return string[]
     */
    public function getAttributes(): array;

    /**
     * Class name for PHP data object corresponded to this repo.
     * @return string
     */
    public function getEntityClass();

    /**
     * Path to entity in DEM ("/path/to/entity").
     * @return string
     */
    public function getEntityPath();

    /**
     * Get one entity using primary key or unique key.
     *
     * @param $key
     * @return DataEntity|null
     */
    public function getOne($key);

    /**
     * Name of the primary key attribute. ElasticSearch operates with simple string ID.
     *
     * @return string
     */
    public function getPrimaryKey();

    /**
     * Get entities according to given conditions.
     *
     * @param string|array $where
     * @param array $bind
     * @param string|array $order
     * @param string $limit
     * @param string $offset
     * @return DataEntity[]
     */
    public function getSet(
        $where = null,
        $bind = null,
        $order = null,
        $limit = null,
        $offset = null
    );

    /**
     * Update one entity (primary key will be extracted from $data).
     *
     * @param DataEntity $data
     * @return int
     */
    public function updateOne($data);

    /**
     * Update entities according to given conditions.
     * Only whose $data attributes that are set directly will be processed.
     *
     * @param DataEntity|array $data
     * @param mixed $where
     * @return int
     */
    public function updateSet($data, $where);
}
