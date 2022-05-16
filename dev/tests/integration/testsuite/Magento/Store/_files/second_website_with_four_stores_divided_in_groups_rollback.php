<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$store = Bootstrap::getObjectManager()->create(Store::class);
if ($store->load('fixture_fourth_store', 'code')->getId()) {
    $store->delete();
}

$store = Bootstrap::getObjectManager()->create(Store::class);
if ($store->load('fixture_fifth_store', 'code')->getId()) {
    $store->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/second_website_with_two_stores_rollback.php');
