<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');

$objectManager = Bootstrap::getObjectManager();
/** @var Order $order */
$order = $objectManager->get(OrderInterfaceFactory::class)->create()->loadByIncrementId('100000001');
$orderStatus = Bootstrap::getObjectManager()->create(
    Status::class
);

$data = [
    'status' => 'example',
    'label' => 'Example',
    'store_labels' => [
        1 => 'Store view example',
    ]
];

$orderStatus->setData($data)->setStatus('example');
$orderStatus->save();

$order->setStatus('example');
$order->save();
