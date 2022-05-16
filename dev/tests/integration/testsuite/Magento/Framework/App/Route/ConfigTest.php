<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\Route;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param string $route
     * @param string $scope
     * @dataProvider getRouteFrontNameDataProvider
     */
    public function testGetRouteFrontName($route, $scope)
    {
        $this->assertEquals(
            $this->objectManager->create(Config::class)->getRouteFrontName($route, $scope),
            $this->objectManager->create(Config::class)->getRouteFrontName($route, $scope)
        );
    }

    public function getRouteFrontNameDataProvider()
    {
        return [
            ['Adminhtml', 'Adminhtml'],
            ['catalog', 'frontend'],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
