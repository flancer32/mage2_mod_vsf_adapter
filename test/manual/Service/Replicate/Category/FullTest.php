<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Test\Flancer32\VsfAdapter\Service\Replicate\Category;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class FullTest
    extends \PHPUnit\Framework\TestCase
{
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Category */
    private $obj;

    protected function setUp()
    {
        /** Get object to test */
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        $this->obj = $obm->get(\Flancer32\VsfAdapter\Service\Replicate\Category::class);
    }


    public function test_execute()
    {
        $req = new \Flancer32\VsfAdapter\Service\Replicate\Category\Request();
        $req->storeId = 2;
        $res = $this->obj->execute($req);
        $this->assertInstanceOf(\Flancer32\VsfAdapter\Service\Replicate\Category\Response::class, $res);

    }
}
