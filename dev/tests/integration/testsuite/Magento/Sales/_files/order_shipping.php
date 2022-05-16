<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Api\Data\InvoiceItemCreationInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\ShipmentItemCreationInterface;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\ShipOrderInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');

$objectManager = Bootstrap::getObjectManager();
/** @var Order $order */
$order = Bootstrap::getObjectManager()->create(Order::class);
$order->loadByIncrementId('100000001');

$order->setData(
    'base_to_global_rate',
    2
)->setData(
    'base_shipping_amount',
    20
)->setData(
    'base_shipping_canceled',
    2
)->setData(
    'base_shipping_invoiced',
    20
)->setData(
    'base_shipping_refunded',
    3
)->setData(
    'is_virtual',
    0
)->save();

$orderItems = $order->getItems();
/** @var OrderItemInterface $orderItem */
$orderItem = array_values($orderItems)[0];

/** @var ShipmentItemCreationInterface $shipmentItem */
$invoiceItem = $objectManager->create(InvoiceItemCreationInterface::class);
$invoiceItem->setOrderItemId($orderItem->getItemId());
$invoiceItem->setQty($orderItem->getQtyOrdered());
/** @var InvoiceOrderInterface $invoiceOrder */
$invoiceOrder = $objectManager->create(InvoiceOrderInterface::class);
$invoiceOrder->execute($order->getEntityId(), false, [$invoiceItem]);

/** @var ShipmentItemCreationInterface $shipmentItem */
$shipmentItem = $objectManager->create(ShipmentItemCreationInterface::class);
$shipmentItem->setOrderItemId($orderItem->getItemId());
$shipmentItem->setQty($orderItem->getQtyOrdered());
/** @var ShipOrderInterface $shipOrder */
$shipOrder = $objectManager->create(ShipOrderInterface::class);
$shipOrder->execute($order->getEntityId(), [$shipmentItem]);
