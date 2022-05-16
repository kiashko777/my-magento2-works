<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Store\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface as ObjectManager;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class StoreSwitcherTest extends TestCase
{
    /**
     * @var StoreSwitcher
     */
    private $storeSwitcher;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @magentoDataFixture Magento/Store/_files/store.php
     * @magentoDataFixture Magento/Store/_files/second_store.php
     * @return void
     * @throws StoreSwitcher\CannotSwitchStoreException
     * @throws NoSuchEntityException
     */
    public function testSwitch(): void
    {
        $redirectUrl = "http://domain.com/?___store=fixture_second_store";
        $expectedUrl = "http://domain.com/";
        $fromStoreCode = 'test';
        /** @var StoreRepositoryInterface $storeRepository */
        $storeRepository = $this->objectManager->create(StoreRepositoryInterface::class);
        $fromStore = $storeRepository->get($fromStoreCode);

        $toStoreCode = 'fixture_second_store';
        /** @var StoreRepositoryInterface $storeRepository */
        $storeRepository = $this->objectManager->create(StoreRepositoryInterface::class);
        $toStore = $storeRepository->get($toStoreCode);

        $this->assertEquals($expectedUrl, $this->storeSwitcher->switch($fromStore, $toStore, $redirectUrl));
    }

    /**
     * Class dependencies initialization
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->storeSwitcher = $this->objectManager->get(StoreSwitcher::class);
    }
}
