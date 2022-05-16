<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var Registry $registry */

use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var Magento\Store\Model\Store $store */
$store = Bootstrap::getObjectManager()->create(Store::class);
$store->load('inactive_store');

if ($store->getId()) {
    $store->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
