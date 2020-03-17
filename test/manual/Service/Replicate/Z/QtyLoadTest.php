<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Test\Flancer32\VsfAdapter\Service\Replicate\Z;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class QtyLoadTest
    extends \PHPUnit\Framework\TestCase
{
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Z\QtyLoad */
    private $obj;

    protected function setUp()
    {
        /** Get object to test */
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        $this->obj = $obm->get(\Flancer32\VsfAdapter\Service\Replicate\Z\QtyLoad::class);
    }


    public function test_exec()
    {
        $storeId = 2;
        $data = $this->obj->exec($storeId);
        $this->assertTrue(\is_array($data) && count($data));
        $this->assertInstanceOf(\Flancer32\VsfAdapter\Service\Replicate\Z\Data\Stock::class, reset($data));
    }
}
