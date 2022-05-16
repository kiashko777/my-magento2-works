<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Paypal\Model\Config;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

Bootstrap::getInstance()->loadArea('Adminhtml');
Bootstrap::getObjectManager()->get(
    MutableScopeConfigInterface::class
)->setValue(
    'carriers/flatrate/active',
    1,
    ScopeInterface::SCOPE_STORE
);
Bootstrap::getObjectManager()->get(
    MutableScopeConfigInterface::class
)->setValue(
    'payment/paypal_express/active',
    1,
    ScopeInterface::SCOPE_STORE
);
$objectManager = Bootstrap::getObjectManager();
/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->setTypeId('simple')
    ->setId(1)
    ->setAttributeSetId(4)
    ->setName('Simple Products')
    ->setSku('simple')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(
        [
            'qty' => 100,
            'is_in_stock' => 1,
            'manage_stock' => 1,
        ]
    )->save();
$product->load(1);

$billingData = [
    'firstname' => 'testname',
    'lastname' => 'lastname',
    'company' => '',
    'email' => 'test@com.com',
    'street' => [
        0 => 'test1',
        1 => '',
    ],
    'city' => 'Test',
    'region_id' => '1',
    'region' => '',
    'postcode' => '9001',
    'country_id' => 'US',
    'telephone' => '11111111',
    'fax' => '',
    'confirm_password' => '',
    'save_in_address_book' => '1',
    'use_for_shipping' => '1',
];

$billingAddress = Bootstrap::getObjectManager()
    ->create(Address::class, ['data' => $billingData]);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');
$shippingAddress->setShippingMethod('flatrate_flatrate');
$shippingAddress->setCollectShippingRates(true);

/** @var $quote Quote */
$quote = $objectManager->create(Quote::class);
$quote->setCustomerIsGuest(
    true
)->setStoreId(
    $objectManager->get(
        StoreManagerInterface::class
    )->getStore()->getId()
)->setReservedOrderId(
    '100000002'
)->setBillingAddress(
    $billingAddress
)->setShippingAddress(
    $shippingAddress
)->addProduct(
    $product,
    10
);
$quote->getShippingAddress()->setShippingMethod('flatrate_flatrate');
$quote->getShippingAddress()->setCollectShippingRates(true);
$quote->getPayment()->setMethod(Config::METHOD_WPS_EXPRESS);

$quoteRepository = $objectManager->get(CartRepositoryInterface::class);
$quoteRepository->save($quote);
$quote = $quoteRepository->get($quote->getId());
$quote->setCustomerEmail('admin@example.com');
