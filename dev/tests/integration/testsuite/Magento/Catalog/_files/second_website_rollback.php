<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get('Magento\Framework\Registry');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$objectManager = Bootstrap::getObjectManager();
$website = $objectManager->get(Magento\Store\Model\Website::class);
$website->load('test_website', 'code');

if ($website->getId()) {
    $website->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

$objectManager->get(StoreManagerInterface::class)->reinitStores();
