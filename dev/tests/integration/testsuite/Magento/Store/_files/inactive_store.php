<?php
/**
 * Create store fixture
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var Store $store */

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$store = Bootstrap::getObjectManager()->create(Store::class);
if (!$store->load('inactive_store', 'code')->getId()) {
    $websiteId = Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getWebsite()
        ->getId();
    $groupId = Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getWebsite()->getDefaultGroupId();
    $store->setCode(
        'inactive_store'
    )->setWebsiteId(
        $websiteId
    )->setGroupId(
        $groupId
    )->setName(
        'Inactive Store'
    )->setSortOrder(
        15
    )->setIsActive(
        0
    );
    $store->save();
}
