<?php
/**
 * Create fixture store
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Store $store */

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$store = Bootstrap::getObjectManager()->create(Store::class);
if (!$store->load('fixture_second_store', 'code')->getId()) {
    $websiteId = Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getWebsite()
        ->getId();
    $groupId = Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getWebsite()->getDefaultGroupId();
    $store->setCode(
        'fixture_second_store'
    )->setWebsiteId(
        $websiteId
    )->setGroupId(
        $groupId
    )->setName(
        'Fixture Store'
    )->setSortOrder(
        10
    )->setIsActive(
        1
    );
    $store->save();
}
