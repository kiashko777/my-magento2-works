<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Store\Model;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class StoreResolverTest extends TestCase
{
    /** @var ObjectManager */
    private $objectManager;

    public function testGetStoreData()
    {
        $methodGetStoresData = new ReflectionMethod(StoreResolver::class, 'getStoresData');
        $methodGetStoresData->setAccessible(true);
        $methodReadStoresData = new ReflectionMethod(StoreResolver::class, 'readStoresData');
        $methodReadStoresData->setAccessible(true);

        $storeResolver = $this->objectManager->get(StoreResolver::class);

        $storesDataRead = $methodReadStoresData->invoke($storeResolver);
        $storesData = $methodGetStoresData->invoke($storeResolver);
        $storesDataCached = $methodGetStoresData->invoke($storeResolver);
        $this->assertEquals($storesDataRead, $storesData);
        $this->assertEquals($storesDataRead, $storesDataCached);
    }

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
