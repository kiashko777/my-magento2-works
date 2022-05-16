<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');

$objectManager = Bootstrap::getObjectManager();

/** @var Order $order */
$order = $objectManager->create('Magento\Sales\Model\Order')
    ->loadByIncrementId('100000001');

$order->setState(
    Order::STATE_NEW
);

$order->setStatus(
    $order->getConfig()->getStateDefaultStatus(
        Order::STATE_NEW
    )
);

$order->save();
