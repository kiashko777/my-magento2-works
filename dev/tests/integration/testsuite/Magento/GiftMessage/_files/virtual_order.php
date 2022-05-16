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

$objectManager = Bootstrap::getObjectManager();

$order = $objectManager->create(Order::class)->loadByIncrementId('100000001');
$order->setIsVirtual(1)->save();
