<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Test\Flancer32\VsfAdapter\Service\Replicate\Product\A;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

use Flancer32\VsfAdapter\Service\Replicate\Product\A\Data\Attr as DAttr;

class AttrLoaderTest
    extends \PHPUnit\Framework\TestCase
{
    /** @var \Flancer32\VsfAdapter\Service\Replicate\Product\A\AttrLoader */
    private $obj;

    protected function setUp()
    {
        /** Get object to test */
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        $this->obj = $obm->get(\Flancer32\VsfAdapter\Service\Replicate\Product\A\AttrLoader::class);
    }


    public function test_exec()
    {
        $storeId = 2;
        $res = $this->obj->exec($storeId);
        $this->assertTrue(is_array($res));
        $item = reset($res);
        $this->assertInstanceOf(DAttr::class, $item);

    }
}
