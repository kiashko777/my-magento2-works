<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\DB\Transaction;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');

$objectManager = Bootstrap::getObjectManager();

/** @var Order $order */
$order = $objectManager->create(Order::class)
    ->loadByIncrementId('100000001');

/** @var InvoiceService $invoiceService */
$invoiceService = $objectManager->create(InvoiceManagementInterface::class);

/** @var Transaction $transaction */
$transaction = $objectManager->create(Transaction::class);

$order->setData(
    'base_to_global_rate',
    1
)->setData(
    'base_to_order_rate',
    1
)->setData(
    'shipping_amount',
    20
)->setData(
    'base_shipping_amount',
    20
);

$invoice = $invoiceService->prepareInvoice($order);
$invoice->register();

$order->setIsInProcess(true);

$items = [];
foreach ($order->getItems() as $orderItem) {
    $items[$orderItem->getId()] = $orderItem->getQtyOrdered();
}
$shipment = $objectManager->get(ShipmentFactory::class)->create($order, $items);
$shipment->register();

$transaction->addObject($invoice)->addObject($shipment)->addObject($order)->save();
