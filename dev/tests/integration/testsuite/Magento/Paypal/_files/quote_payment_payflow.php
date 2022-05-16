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
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Sales\Model\Order\Payment;
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
/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    'simple'
)->setId(
    1
)->setAttributeSetId(
    4
)->setName(
    'Simple Products'
)->setSku(
    'simple'
)->setPrice(
    10
)->setStockData(
    ['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 100]
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->save();
$product->load(1);

$addressData = [
    'region' => 'CA',
    'postcode' => '11111',
    'lastname' => 'lastname',
    'firstname' => 'firstname',
    'street' => 'street',
    'city' => 'Los Angeles',
    'email' => 'admin@example.com',
    'telephone' => '11111111',
    'country_id' => 'US',
];

$billingData = [
    'address_id' => '',
    'firstname' => 'testname',
    'lastname' => 'lastname',
    'company' => '',
    'email' => 'test@com.com',
    'street' => [0 => 'test1', 1 => ''],
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

$billingAddress = Bootstrap::getObjectManager()->create(
    Address::class,
    ['data' => $billingData]
);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');
$shippingAddress->setShippingMethod('flatrate_flatrate');
$shippingAddress->setCollectShippingRates(true);

/** @var $quote Quote */
$quote = Bootstrap::getObjectManager()->create(Quote::class);
$quote->setCustomerIsGuest(
    true
)->setStoreId(
    Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getStore()->getId()
)->setReservedOrderId(
    'test02'
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
$quote->collectTotals()->save();

$payment = Bootstrap::getObjectManager()->create(
    Payment::class
);
$payment->setMethod(Config::METHOD_WPS_EXPRESS);

$quote->getPayment()->setMethod(Config::METHOD_WPS_EXPRESS)->save();

/** @var $service CartManagementInterface */
$service = Bootstrap::getObjectManager()
    ->create(CartManagementInterface::class);
$order = $service->submit($quote, ['increment_id' => '100000001']);
