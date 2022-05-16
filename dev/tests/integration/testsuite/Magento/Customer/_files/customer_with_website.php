<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var ObjectManagerInterface $objectManager */

use Magento\Customer\Model\Customer;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManager;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var StoreManager $store */
$store = $objectManager->get(StoreManager::class);

/** @var Customer $customer */
$customer = $objectManager->create(
    Customer::class,
    [
        'data' => [
            'website_id' => $store->getDefaultStoreView()->getWebsiteId(),
            'email' => 'john.doe@magento.com',
            'store_id' => $store->getDefaultStoreView()->getId(),
            'is_active' => true,
            'firstname' => 'John',
            'lastname' => 'Doe',
        ]
    ]
);
$customer->save();
