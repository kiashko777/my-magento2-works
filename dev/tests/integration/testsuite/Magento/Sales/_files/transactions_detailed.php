<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Magento\Sales\Model\Order\Payment $payment */
$payment = $objectManager->create(Payment::class);
$payment->setMethod('checkmo');

/** @var Order $order */
$order = $objectManager->create(Order::class);
$order->setIncrementId('100000006')->setSubtotal(100)->setBaseSubtotal(100)->setCustomerIsGuest(true)
    ->setPayment($payment);

$payment->setTransactionId('trx_auth');
$payment->setIsTransactionClosed(true);
$payment->setTransactionAdditionalInfo('auth_key', 'data');
$payment->addTransaction(Transaction::TYPE_AUTH);

$payment->resetTransactionAdditionalInfo();

$payment->setTransactionId('trx_capture');
$payment->setIsTransactionClosed(false);
$payment->setTransactionAdditionalInfo('capture_key', 'data');
$payment->setParentTransactionId('trx_auth');
$payment->addTransaction(Transaction::TYPE_CAPTURE);

$order->save();
