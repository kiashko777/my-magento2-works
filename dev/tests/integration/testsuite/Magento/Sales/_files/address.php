<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @var $address Address */

use Magento\Sales\Model\Order\Address;
use Magento\TestFramework\Helper\Bootstrap;

$address = Bootstrap::getObjectManager()->create(
    Address::class
);
$address->setRegion(
    'CA'
)->setPostcode(
    '90210'
)->setFirstname(
    'a_unique_firstname'
)->setLastname(
    'lastname'
)->setStreet(
    'street'
)->setCity(
    'Beverly Hills'
)->setEmail(
    'admin@example.com'
)->setTelephone(
    '1111111111'
)->setCountryId(
    'US'
)->setAddressType(
    'shipping'
)->save();
