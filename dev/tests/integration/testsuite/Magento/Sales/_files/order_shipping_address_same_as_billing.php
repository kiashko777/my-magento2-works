<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $billingAddress Address */

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Payment;
use Magento\TestFramework\Helper\Bootstrap;

$billingAddress = Bootstrap::getObjectManager()->create(
    Address::class,
    [
        'data' => [
            'firstname' => 'guest',
            'lastname' => 'guest',
            'email' => 'customer@example.com',
            'street' => 'street',
            'city' => 'Los Angeles',
            'region' => 'CA',
            'postcode' => '1',
            'country_id' => 'US',
            'telephone' => '1',
        ]
    ]
);
$billingAddress->setAddressType('billing');

$shippingAddress = clone $billingAddress;
$shippingAddress->setId(null)->setAddressType('shipping');

/** @var $order Order */
$order = Bootstrap::getObjectManager()->create(Order::class);
$order->loadByIncrementId('100000001');
$clonedOrder = clone $order;
$order->setIncrementId('100000002');
$order->save();

/** @var $payment Payment */
$payment = Bootstrap::getObjectManager()->create(
    Payment::class
);
$payment->setMethod('checkmo');

$order = $clonedOrder;
$order->setId(
    null
)->setCustomerEmail(
    'customer@example.com'
)->setBillingAddress(
    $billingAddress
)->setShippingAddress(
    $shippingAddress
)->setPayment(
    $payment
);
$order->save();
