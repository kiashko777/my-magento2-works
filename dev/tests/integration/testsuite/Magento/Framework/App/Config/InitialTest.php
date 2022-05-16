<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\App\Config;

use Magento\Framework\App\Config\Initial as Config;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class InitialTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function testGetMetadata()
    {
        $this->assertEquals(
            $this->objectManager->create(Config::class)->getMetadata(),
            $this->objectManager->create(Config::class)->getMetadata()
        );
    }

    /**
     * @param string $scope
     * @dataProvider getDataDataProvider
     */
    public function testGetData($scope)
    {
        $this->assertEquals(
            $this->objectManager->create(Config::class)->getData($scope),
            $this->objectManager->create(Config::class)->getData($scope)
        );
    }

    public function getDataDataProvider()
    {
        return [
            ['default'],
            ['stores|default'],
            ['websites|default']
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
