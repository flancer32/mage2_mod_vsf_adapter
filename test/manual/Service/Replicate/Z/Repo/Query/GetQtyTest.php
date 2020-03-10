<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Test\Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

use Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query\GetQty as Query;

class AttrLoaderTest
    extends \PHPUnit\Framework\TestCase
{
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query\GetQty */
    private $obj;

    protected function setUp()
    {
        /** Get object to test */
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        $this->obj = $obm->get(\Flancer32\VsfAdapter\Service\Replicate\Z\Repo\Query\GetQty::class);
    }


    public function test_build()
    {
        $query = $this->obj->build();
        $conn = $query->getConnection();
        $rs = $conn->fetchAll($query, [Query::BND_STORE_ID => 2]);
        $this->assertTrue(\is_array($rs) && count($rs));
    }
}
