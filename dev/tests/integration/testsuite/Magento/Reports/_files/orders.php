<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Model\ResourceModel\Report\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');

// refresh report statistics
/** @var Order $reportResource */
$reportResource = Bootstrap::getObjectManager()->create(
    Order::class
);
$reportResource->aggregate();
