<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var ObjectManagerInterface $objectManager */

use Magento\Customer\Model\Customer;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManager;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var StoreManager $store */
$store = $objectManager->get(StoreManager::class);

/** @var $customer Customer */
$customer = $objectManager->create(Customer::class);
$customer->setWebsiteId($store->getDefaultStoreView()->getWebsiteId());
$customer->loadByEmail('john.doe@magento.com');
if ($customer->getId()) {
    $customer->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
