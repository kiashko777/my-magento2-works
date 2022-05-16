<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Customer $customer */
$customer = Bootstrap::getObjectManager()
    ->create(Customer::class);
/** @var CustomerRegistry $customerRegistry */
$customerRegistry = Bootstrap::getObjectManager()->get(CustomerRegistry::class);

$customerData = [
    'group_id' => 1,
    'website_id' => 1,
    'store_id' => 1,
    'firstname' => 'test firstname',
    'lastname' => 'test lastname',
    'email' => 'customer@example.com',
    'default_billing' => 1,
    'default_shipping' => 1,
    'password' => '123123q',
    'attribute_set_id' => 1,
];
$customer->setData($customerData);
$customer->setId(1);

/** @var Address $addressOne */
$addressOne = Bootstrap::getObjectManager()->create(
    Address::class
);
$addressOneData = [
    'firstname' => 'test firstname',
    'lastname' => 'test lastname',
    'street' => ['test street'],
    'city' => 'test city',
    'region_id' => 10,
    'country_id' => 'US',
    'postcode' => '01001',
    'telephone' => '+7000000001',
    'entity_id' => 1,
];
$addressOne->setData($addressOneData);
$customer->addAddress($addressOne);

/** @var Address $addressTwo */
$addressTwo = Bootstrap::getObjectManager()->create(
    Address::class
);
$addressTwoData = [
    'firstname' => 'test firstname',
    'lastname' => 'test lastname',
    'street' => ['test street'],
    'city' => 'test city',
    'region_id' => 10,
    'country_id' => 'US',
    'postcode' => '01001',
    'telephone' => '+7000000001',
    'entity_id' => 2,
];
$addressTwo->setData($addressTwoData);
$customer->addAddress($addressTwo);

/** @var Address $addressThree */
$addressThree = Bootstrap::getObjectManager()->create(
    Address::class
);
$addressThreeData = [
    'firstname' => 'removed firstname',
    'lastname' => 'removed lastname',
    'street' => ['removed street'],
    'city' => 'removed city',
    'region_id' => 10,
    'country_id' => 'US',
    'postcode' => '01001',
    'telephone' => '+7000000001',
    'entity_id' => 3,
];
$addressThree->setData($addressThreeData);
$customer->addAddress($addressThree);

$customer->save();
$customerRegistry->remove($customer->getId());
