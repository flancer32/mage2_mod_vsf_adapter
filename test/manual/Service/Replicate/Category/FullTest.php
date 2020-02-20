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
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Category\Full */
    private $obj;

    protected function setUp()
    {
        /** Get object to test */
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        $this->obj = $obm->get(\Flancer32\VsfAdapter\Service\Replicate\Category\Full::class);
    }


    public function test_execute()
    {
        $req = new \Flancer32\VsfAdapter\Service\Replicate\Category\Full\Request();
        $req->storeId = 2;
        $res = $this->obj->execute($req);
        $this->assertInstanceOf(\Flancer32\VsfAdapter\Service\Replicate\Category\Full\Response::class, $res);

    }
}
