<?php
/**
 * Test case for \Magento\Framework\Profiler
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Reflection;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class MethodsMapTest extends TestCase
{
    /** @var MethodsMap */
    private $object;

    public function testGetMethodsMap()
    {
        $data = $this->object->getMethodsMap(MethodsMap::class);
        $this->assertArrayHasKey('getMethodsMap', $data);
        $cachedData = $this->object->getMethodsMap(MethodsMap::class);
        $this->assertEquals($data, $cachedData);
    }

    public function testGetMethodParams()
    {
        $data = $this->object->getMethodParams(
            MethodsMap::class,
            'getMethodParams'
        );
        $this->assertCount(2, $data);
        $cachedData = $this->object->getMethodParams(
            MethodsMap::class,
            'getMethodParams'
        );
        $this->assertEquals($data, $cachedData);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->object = $objectManager->create(
            MethodsMap::class
        );
    }
}
