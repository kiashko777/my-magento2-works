<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var $repository CustomerRepositoryInterface */
$repository = $objectManager->create(CustomerRepositoryInterface::class);
$customer = $objectManager->create(CustomerInterface::class);

/** @var Magento\Customer\Api\Data\CustomerInterface $customer */
$customer->setWebsiteId(1)
    ->setEmail('customer@example.com')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setPrefix('Mr.')
    ->setFirstname('John')
    ->setMiddlename('A')
    ->setLastname('Smith')
    ->setSuffix('Esq.')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1)
    ->setTaxvat('12')
    ->setGender(0);
$repository->save($customer, 'password');
