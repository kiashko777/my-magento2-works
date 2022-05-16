<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Store\Model\App;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class EmulationTest extends TestCase
{
    /**
     * @var Emulation
     */
    protected $_model;

    /**
     * @covers \Magento\Store\Model\App\Emulation::startEnvironmentEmulation
     * @covers \Magento\Store\Model\App\Emulation::stopEnvironmentEmulation
     */
    public function testEnvironmentEmulation()
    {
        $this->_model = Bootstrap::getObjectManager()
            ->create(Emulation::class);
        Bootstrap::getInstance()
            ->loadArea(FrontNameResolver::AREA_CODE);
        $design = Bootstrap::getObjectManager()
            ->get(DesignInterface::class);

        $this->_model->startEnvironmentEmulation(1);
        $this->_model->stopEnvironmentEmulation();
        $this->assertEquals(FrontNameResolver::AREA_CODE, $design->getArea());
    }
}
