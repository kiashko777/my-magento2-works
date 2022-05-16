<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$repository = $objectManager->create(CustomerRepositoryInterface::class);
$customer = $objectManager->create(Customer::class);
$customerRegistry = $objectManager->get(CustomerRegistry::class);
$customer->setWebsiteId(1)
    ->setId(1)
    ->setEmail('customer1@example.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('John')
    ->setLastname('Smith')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1);
$customer->isObjectNew(true);
$customer->save();

$customer->setWebsiteId(1)
    ->setId(2)
    ->setEmail('customer2@example.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('Jane')
    ->setLastname('Smith')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1);
$customer->isObjectNew(true);
$customer->save();

$customerRegistry->remove($customer->getId());
