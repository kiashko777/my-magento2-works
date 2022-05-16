<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Registry $registry */

use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
/** Delete the second website **/
$website = Bootstrap::getObjectManager()->create(Website::class);
/** @var $website Website */
$websiteId = $website->load('second', 'code')->getId();
if ($websiteId) {
    $website->delete();
}
$website2 = Bootstrap::getObjectManager()->create(Website::class);
/** @var $website Website */
$websiteId2 = $website2->load('third', 'code')->getId();
if ($websiteId2) {
    $website2->delete();
}

$store = Bootstrap::getObjectManager()->create(Store::class);
if ($store->load('second_store_view', 'code')->getId()) {
    $store->delete();
}

$store2 = Bootstrap::getObjectManager()->create(Store::class);
if ($store2->load('third_store_view', 'code')->getId()) {
    $store2->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
