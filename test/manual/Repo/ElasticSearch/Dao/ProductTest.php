<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Test\Flancer32\VsfAdapter\Repo\ElasticSearch\Dao;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class ProductTest
    extends \PHPUnit\Framework\TestCase
{
    /** @var \Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Product */
    private $obj;

    protected function setUp()
    {
        /** Get object to test */
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        $this->obj = $obm->get(\Flancer32\VsfAdapter\Repo\ElasticSearch\Dao\Product::class);
    }


    public function test_getOne()
    {
        $res = $this->obj->getOne(21);
        $this->assertInstanceOf(\Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product::class, $res);
    }

    public function test_getSet()
    {
        $where = null;
        $bind = null;
        $order = null;
        $limit = 5;
        $offset = 3;
        $res = $this->obj->getSet($where, $bind, $order, $limit, $offset);
        $one = reset($res);
        $this->assertInstanceOf(\Flancer32\VsfAdapter\Repo\ElasticSearch\Data\Product::class, $one);
    }
}
