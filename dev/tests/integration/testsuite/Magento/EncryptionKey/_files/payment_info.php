<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Payment $paymentInfo */
$paymentInfo = $objectManager->create(Payment::class);
$paymentInfo->setMethod('Cc')->setData('cc_number_enc', '1111111111');

/** @var Order $order */
$order = $objectManager->create(Order::class);
$order->setIncrementId(
    '100000001'
)->setPayment(
    $paymentInfo
);
$order->save();
