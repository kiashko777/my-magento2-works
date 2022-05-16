<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_address.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/products.php');

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple');
/** @var Address $quoteShippingAddress */
$quoteShippingAddress = $objectManager->create(Address::class);

/** @var AccountManagementInterface $accountManagement */
$accountManagement = $objectManager->create(AccountManagementInterface::class);

/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->create(CustomerRepositoryInterface::class);
$customer = $customerRepository->getById(1);

/** @var AddressRepositoryInterface $addressRepository */
$addressRepository = $objectManager->create(AddressRepositoryInterface::class);
$quoteShippingAddress->importCustomerAddressData($addressRepository->getById(1));

/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
$quote->setStoreId(
    1
)->setIsActive(
    true
)->setIsMultiShipping(
    false
)->assignCustomerWithAddressChange(
    $customer
)->setShippingAddress(
    $quoteShippingAddress
)->setBillingAddress(
    $quoteShippingAddress
)->setCheckoutMethod(
    'customer'
)->setPasswordHash(
    $accountManagement->getPasswordHash('password')
)->setReservedOrderId(
    'test_order_1'
)->setCustomerEmail(
    'aaa@aaa.com'
)->addProduct(
    $product,
    2
);
$quote->save();
