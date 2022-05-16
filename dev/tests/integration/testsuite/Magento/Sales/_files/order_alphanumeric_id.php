<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');

/** @var Order $order */
$order = Bootstrap::getObjectManager()->create(Order::class);

$order->loadByIncrementId('100000001');
$order->setIncrementId('M00000001');
$order->save();
