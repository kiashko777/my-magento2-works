<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var StoreManagerInterface $storeManager */
$storeManager = Bootstrap::getObjectManager()->get(StoreManagerInterface::class);

/** @var Store $store */
$store = Bootstrap::getObjectManager()->create(Store::class);
$storeCode = 'fixturestore';

if (!$store->load($storeCode)->getId()) {
    $store->setCode($storeCode)
        ->setWebsiteId($storeManager->getWebsite()->getId())
        ->setGroupId($storeManager->getWebsite()->getDefaultGroupId())
        ->setName('Fixture Store')
        ->setSortOrder(10)
        ->setIsActive(1);
    $store->save();
}
