<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @var Registry $registry */

use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var Website $website */
$website = Bootstrap::getObjectManager()->create(Website::class);
$website->load('test');

if ($website->getId()) {
    $website->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

/* Refresh stores memory cache */
Bootstrap::getObjectManager()->get(
    StoreManagerInterface::class
)->reinitStores();
