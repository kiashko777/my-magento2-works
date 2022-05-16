<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Item;
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

$payment = Bootstrap::getObjectManager()->create(
    Payment::class
);
$payment->setMethod('checkmo');

$orderItem = Bootstrap::getObjectManager()->create(
    Item::class
);
$orderItem->setProductId(
    1
)->setProductType(
    \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE
)->setBasePrice(
    100
)->setQtyOrdered(
    1
);
$orderItem->setProductOptions(['additional_options' => ['additional_option_key' => 'additional_option_value']]);

$order = Bootstrap::getObjectManager()->create(Order::class);
$order->setCustomerEmail(
    'mail@to.co'
)->addItem(
    $orderItem
)->setIncrementId(
    '100000001'
)->setCustomerIsGuest(
    true
)->setStoreId(
    1
)->setEmailSent(
    1
)->setBillingAddress(
    $billingAddress
)->setPayment(
    $payment
);
$order->save();
