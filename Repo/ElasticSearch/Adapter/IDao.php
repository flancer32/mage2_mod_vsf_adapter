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
     * Delete set of entities using $where condition.
     *
     * @param $where
     * @return mixed
     */
    public function deleteSet($where);

    /**
     * Class name for PHP data object corresponded to this repo.
     * @return string
     */
    public function getEntityClass();

    /**
     * Name of the primary key attribute. ElasticSearch operates with simple string ID.
     *
     * @return string
     */
    public function getPrimaryKey();
}
