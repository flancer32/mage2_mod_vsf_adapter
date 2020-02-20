<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

namespace Test\Flancer32\VsfAdapter\Helper;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class ConfigTest
    extends \PHPUnit\Framework\TestCase
{
    /** @var \Flancer32\VsfAdapter\Helper\Config */
    private $obj;

    protected function setUp()
    {
        /** Get object to test */
        $obm = \Magento\Framework\App\ObjectManager::getInstance();
        $this->obj = $obm->get(\Flancer32\VsfAdapter\Helper\Config::class);
    }


    public function test_all()
    {
        $res = $this->obj->getConfigEsHost();
        $this->assertTrue(is_string($res));

        $res = $this->obj->getConfigEsIndexPrefix();
        $this->assertTrue(is_string($res));

        $res = $this->obj->getConfigEsPort();
        $this->assertTrue(is_int($res));

        $res = $this->obj->getConfigEsScheme();
        $this->assertTrue(is_string($res));
    }
}
