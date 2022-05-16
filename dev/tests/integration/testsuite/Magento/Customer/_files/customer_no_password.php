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
/** @var $repository CustomerRepositoryInterface */
$repository = $objectManager->create(CustomerRepositoryInterface::class);
$customer = $objectManager->create(Customer::class);
/** @var CustomerRegistry $customerRegistry */
$customerRegistry = $objectManager->get(CustomerRegistry::class);
/** @var Magento\Customer\Model\Customer $customer */
$customer->setWebsiteId(1)
    ->setId(1)
    ->setEmail('customer@example.com')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setPrefix('Mr.')
    ->setFirstname('John')
    ->setMiddlename('A')
    ->setLastname('Smith')
    ->setSuffix('Esq.')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1)
    ->setTaxvat('12')
    ->setGender(0);

$customer->isObjectNew(true);
$customer->save();
$customerRegistry->remove($customer->getId());
