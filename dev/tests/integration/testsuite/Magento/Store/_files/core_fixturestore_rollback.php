<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var ObjectManagerInterface $objectManager */

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var Store $store */
$store = $objectManager->create(Store::class);
$storeCode = 'fixturestore';
$store->load($storeCode);
if ($store->getId()) {
    $store->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
