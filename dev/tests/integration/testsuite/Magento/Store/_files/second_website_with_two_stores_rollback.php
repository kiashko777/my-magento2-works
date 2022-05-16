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
$website = Bootstrap::getObjectManager()->create(Website::class);
/** @var $website Website */
$websiteId = $website->load('test', 'code')->getId();
if ($websiteId) {
    $website->delete();
}
$store = Bootstrap::getObjectManager()->create(Store::class);
if ($store->load('fixture_second_store', 'code')->getId()) {
    $store->delete();
}

$store = Bootstrap::getObjectManager()->create(Store::class);
if ($store->load('fixture_third_store', 'code')->getId()) {
    $store->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
