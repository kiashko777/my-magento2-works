<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Helper;

use Magento\TestFramework\Helper\Config;
use Magento\TestFramework\Helper\Factory;
use PHPUnit\Framework\TestCase;
use stdClass;

class FactoryTest extends TestCase
{
    public function testGetHelper()
    {
        $helper = Factory::getHelper(Config::class);
        $this->assertNotEmpty($helper);

        $helperNew = Factory::getHelper(Config::class);
        $this->assertSame($helper, $helperNew, 'Factory must cache instances of helpers.');
    }

    public function testSetHelper()
    {
        $helper = new stdClass();
        Factory::setHelper(Config::class, $helper);
        $helperGot = Factory::getHelper(Config::class);
        $this->assertSame($helper, $helperGot, 'The helper must be used, when requested again');

        $helperNew = new stdClass();
        Factory::setHelper(Config::class, $helperNew);
        $helperGot = Factory::getHelper(Config::class);
        $this->assertSame($helperNew, $helperGot, 'The helper must be changed upon new setHelper() method');
    }
}
