<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Customer\Model\Customer;
use Magento\TestFramework\Helper\Bootstrap;

$customer = Bootstrap::getObjectManager()->create(
    Customer::class
);
$customer->setWebsiteId(
    1
)->setId(
    5
)->setEntityTypeId(
    1
)->setAttributeSetId(
    1
)->setEmail(
    'customer5@example.com'
)->setPassword(
    'password'
)->setGroupId(
    1
)->setStoreId(
    1
)->setIsActive(
    1
)->setFirstname(
    'Firstname'
)->setLastname(
    'Lastname'
);
$customer->isObjectNew(true);
$customer->save();
