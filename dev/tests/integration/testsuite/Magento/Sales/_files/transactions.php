<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\TestFramework\Helper\Bootstrap;

$payment = Bootstrap::getObjectManager()->create(
    Payment::class
);
$payment->setMethod('checkmo');

$order = Bootstrap::getObjectManager()->create(Order::class);
$order->setIncrementId(
    '100000001'
)->setSubtotal(
    100
)->setBaseSubtotal(
    100
)->setCustomerIsGuest(
    true
)->setPayment(
    $payment
);

$payment->setTransactionId('trx1');
$payment->addTransaction(Transaction::TYPE_AUTH);

$order->save();
