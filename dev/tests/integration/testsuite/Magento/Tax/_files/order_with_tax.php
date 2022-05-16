<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Model\Order;
use Magento\Tax\Model\Sales\Order\Tax;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');

$objectManager = Bootstrap::getObjectManager();
/** @var Order $order */
$order = $objectManager->create(Order::class);
$order->loadByIncrementId('100000001')->setBaseToGlobalRate(2)->save();

/** @var Tax $tax */
$tax = $objectManager->create(Tax::class);
$tax->setData(
    [
        'order_id' => $order->getId(),
        'code' => 'tax_code',
        'title' => 'Tax Title',
        'hidden' => 0,
        'percent' => 10,
        'priority' => 1,
        'position' => 1,
        'amount' => 10,
        'base_amount' => 10,
        'process' => 1,
        'base_real_amount' => 10,
    ]
);
$tax->save();
