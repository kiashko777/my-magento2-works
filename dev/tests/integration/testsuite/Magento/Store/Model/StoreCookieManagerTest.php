<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Store\Model;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class StoreCookieManagerTest extends TestCase
{
    /**
     * @var StoreCookieManager
     */
    protected $storeCookieManager;

    /**
     * @var array
     */
    protected $existingCookies;

    public function testSetCookie()
    {
        $storeCode = 'store code';
        $store = $this->createPartialMock(Store::class, ['getStorePath', 'getCode']);
        $store->expects($this->once())->method('getStorePath')->willReturn('/');
        $store->expects($this->once())->method('getCode')->willReturn($storeCode);

        $this->assertArrayNotHasKey(StoreCookieManager::COOKIE_NAME, $_COOKIE);
        $this->storeCookieManager->setStoreCookie($store);
        $this->assertArrayHasKey(StoreCookieManager::COOKIE_NAME, $_COOKIE);
        $this->assertEquals($storeCode, $_COOKIE[StoreCookieManager::COOKIE_NAME]);
    }

    protected function setUp(): void
    {
        $this->storeCookieManager = Bootstrap::getObjectManager()->create(
            StoreCookieManager::class
        );
        $this->existingCookies = $_COOKIE;
    }

    protected function tearDown(): void
    {
        $_COOKIE = $this->existingCookies;
    }
}
