<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Paypal\Model\Config;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_two_addresses.php');

Bootstrap::getInstance()->loadArea('Adminhtml');

$objectManager = Bootstrap::getObjectManager();

$objectManager->get(
    MutableScopeConfigInterface::class
)->setValue('carriers/flatrate/active', 1, ScopeInterface::SCOPE_STORE);
$objectManager->get(MutableScopeConfigInterface::class)
    ->setValue('payment/paypal_express/active', 1, ScopeInterface::SCOPE_STORE);

/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->create(CustomerRepositoryInterface::class);
$customer = $customerRepository->getById(1);

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->setTypeId('simple')
    ->setId(1)
    ->setAttributeSetId(4)
    ->setName('Simple Products')
    ->setSku('simple')
    ->setPrice(10)
    ->setStockData([
        'use_config_manage_stock' => 1,
        'qty' => 100,
        'is_qty_decimal' => 0,
        'is_in_stock' => 100,
    ])
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->save();
$product->load(1);

$customerBillingAddress = $objectManager->create(\Magento\Customer\Model\Address::class);
$customerBillingAddress->load(1);
$billingAddressDataObject = $customerBillingAddress->getDataModel();
$billingAddress = $objectManager->create(Address::class);
$billingAddress->importCustomerAddressData($billingAddressDataObject);
$billingAddress->setAddressType('billing');

/** @var \Magento\Customer\Model\Address $customerShippingAddress */
$customerShippingAddress = $objectManager->create(\Magento\Customer\Model\Address::class);
$customerShippingAddress->load(2);
$shippingAddressDataObject = $customerShippingAddress->getDataModel();
$shippingAddress = $objectManager->create(Address::class);
$shippingAddress->importCustomerAddressData($shippingAddressDataObject);
$shippingAddress->setAddressType('shipping');

$shippingAddress->setShippingMethod('flatrate_flatrate');
$shippingAddress->setCollectShippingRates(true);

/** @var $quote Quote */
$quote = $objectManager->create(Quote::class);
$quote->setCustomerIsGuest(false)
    ->setCustomerId($customer->getId())
    ->setCustomer($customer)
    ->setStoreId($objectManager->get(StoreManagerInterface::class)->getStore()->getId())
    ->setReservedOrderId('test02')
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->addProduct($product, 10);
$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
$quote->getShippingAddress()->setCollectShippingRates(true);
$quote->getPayment()->setMethod(Config::METHOD_WPS_EXPRESS);

/** @var CartRepositoryInterface $quoteRepository */
$quoteRepository = $objectManager->create(CartRepositoryInterface::class);
$quoteRepository->save($quote);
$quote = $quoteRepository->get($quote->getId());
