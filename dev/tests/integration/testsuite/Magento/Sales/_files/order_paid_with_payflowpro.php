<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$addressData = include __DIR__ . '/address_data.php';

$billingAddress = Bootstrap::getObjectManager()->create(
    Address::class,
    ['data' => $addressData]
);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

$payment = Bootstrap::getObjectManager()->create(
    Payment::class
);
$payment->setMethod('payflowpro')->setCcExpMonth('5')->setCcLast4('0005')->setCcType('AE')->setCcExpYear('2016');

$order = Bootstrap::getObjectManager()->create(Order::class);
$order->setIncrementId(
    '100000001'
)->setSubtotal(
    100
)->setBaseSubtotal(
    100
)->setCustomerEmail(
    'admin@example.com'
)->setCustomerIsGuest(
    true
)->setBillingAddress(
    $billingAddress
)->setShippingAddress(
    $shippingAddress
)->setStoreId(
    Bootstrap::getObjectManager()->get(
        StoreManagerInterface::class
    )->getStore()->getId()
)->setPayment(
    $payment
);

$order->save();
