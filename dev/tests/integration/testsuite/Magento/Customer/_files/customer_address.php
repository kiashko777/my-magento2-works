<?php
/**
 * Customer address fixture with entity_id = 1
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Model\CustomerRegistry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Address $customerAddress */
$customerAddress = $objectManager->create(Address::class);
/** @var CustomerRegistry $customerRegistry */
$customerRegistry = $objectManager->get(CustomerRegistry::class);
$customerAddress->isObjectNew(true);
$customerAddress->setData(
    [
        'entity_id' => 1,
        'attribute_set_id' => 2,
        'telephone' => 3468676,
        'postcode' => 75477,
        'country_id' => 'US',
        'city' => 'CityM',
        'company' => 'CompanyName',
        'street' => 'Green str, 67',
        'lastname' => 'Smith',
        'firstname' => 'John',
        'parent_id' => 1,
        'region_id' => 1,
    ]
);
$customerAddress->save();

/** @var AddressRepositoryInterface $addressRepository */
$addressRepository = $objectManager->get(AddressRepositoryInterface::class);
$customerAddress = $addressRepository->getById(1);
$customerAddress->setCustomerId(1);
$customerAddress = $addressRepository->save($customerAddress);
$customerRegistry->remove($customerAddress->getCustomerId());
/** @var AddressRegistry $addressRegistry */
$addressRegistry = $objectManager->get(AddressRegistry::class);
$addressRegistry->remove($customerAddress->getId());
