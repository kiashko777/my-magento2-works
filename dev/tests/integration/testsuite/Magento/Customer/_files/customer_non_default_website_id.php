<?php
/**
 * Create customer and attach it to custom website with code newwebsite
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @var Website $website
 */

use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;

$website = Bootstrap::getObjectManager()->create(Website::class);
$website->setName('new Website')->setCode('newwebsite')->save();

$websiteId = $website->getId();
$storeManager = Bootstrap::getObjectManager()
    ->get(StoreManager::class);
$storeManager->reinitStores();
$customer = Bootstrap::getObjectManager()->create(
    Customer::class
);
/** @var Magento\Customer\Model\Customer $customer */
$customer->setWebsiteId(
    $websiteId
)->setId(
    1
)->setEntityTypeId(
    1
)->setAttributeSetId(
    1
)->setEmail(
    'customer2@example.com'
)->setPassword(
    'password'
)->setGroupId(
    1
)->setStoreId(
    $website->getStoreId()
)->setIsActive(
    1
)->setFirstname(
    'Firstname'
)->setLastname(
    'Lastname'
)->setDefaultBilling(
    1
)->setDefaultShipping(
    1
);
$customer->isObjectNew(true);

/** @var Address $addressOne */
$addressOne = Bootstrap::getObjectManager()->create(
    Address::class
);
$addressOneData = [
    'firstname' => 'Firstname',
    'lastname' => 'LastName',
    'street' => ['test street'],
    'city' => 'test city',
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
    'country_id' => 'US',
    'postcode' => '01001',
    'telephone' => '+7000000001',
    'entity_id' => 3,
];
$addressThree->setData($addressThreeData);
$customer->addAddress($addressThree);
$customer->save();
