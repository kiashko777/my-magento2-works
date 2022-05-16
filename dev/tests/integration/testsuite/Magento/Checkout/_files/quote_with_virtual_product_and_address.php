<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_address.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_virtual.php');

$objectManager = Bootstrap::getObjectManager();
/** @var Address $quoteShippingAddress */
$quoteShippingAddress = $objectManager->create(
    Address::class
);
/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->create(
    CustomerRepositoryInterface::class
);
/** @var AddressRepositoryInterface $addressRepository */
$addressRepository = $objectManager->create(
    AddressRepositoryInterface::class
);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('virtual-product');
$quoteShippingAddress->importCustomerAddressData($addressRepository->getById(1));
/** @var CustomerRegistry $customerRegistry */
$customerRegistry = Bootstrap::getObjectManager()->create(CustomerRegistry::class);
$customer = $customerRegistry->retrieve(1);
/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
$quote->setStoreId(1)
    ->setIsActive(true)
    ->setIsMultiShipping(false)
    ->assignCustomerWithAddressChange($customerRepository->getById($customer->getId()))
    ->setShippingAddress($quoteShippingAddress)
    ->setBillingAddress($quoteShippingAddress)
    ->setCheckoutMethod($customer->getMode())
    ->setPasswordHash($customer->encryptPassword($customer->getPassword()))
    ->setReservedOrderId('test_order_with_virtual_product')
    ->setEmail('store@example.com')
    ->addProduct($product->load($product->getId()), 1);

$quote->collectTotals()->save();

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = $objectManager
    ->create(QuoteIdMaskFactory::class)
    ->create();
$quoteIdMask->setQuoteId($quote->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();
