<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Paypal\Model\Config;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Payment;
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
$payment->setMethod(Config::METHOD_PAYFLOWPRO);

$order = Bootstrap::getObjectManager()->create(Order::class);
$order->setCustomerEmail('co@co.co')
    ->setIncrementId('100000001')
    ->setSubtotal(100)
    ->setBaseSubtotal(100)
    ->setBaseGrandTotal(100)
    ->setBaseCurrencyCode('USD')
    ->setCustomerIsGuest(true)
    ->setBillingAddress($billingAddress)
    ->setShippingAddress($shippingAddress)
    ->setPayment($payment);
$order->save();
