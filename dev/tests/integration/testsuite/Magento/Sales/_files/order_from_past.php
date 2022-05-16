<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var OrderRepositoryInterface $orderRepository */
$orderRepository = $objectManager->create(OrderRepositoryInterface::class);
/** @var \Magento\Framework\Stdlib\DateTime\DateTime $dateTime */
$dateTime = $objectManager->create(DateTimeFactory::class)
    ->create();
/** @var Order $order */
$order = $objectManager->create(Magento\Sales\Model\Order::class)->loadByIncrementId('100000001');
$newOrderCreatedAtTimestamp = $dateTime->timestamp($order->getCreatedAt()) - 864000;
$newOrderCreatedDate = $dateTime->date(null, $newOrderCreatedAtTimestamp);
$order->setCreatedAt($newOrderCreatedDate);
$orderRepository->save($order);
