<?php
/**
 * Not paid invoice fixture for online payment method.
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\DB\Transaction;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order_paid_with_payflowpro.php');
/** @var Order $order */
$order = $objectManager->get(OrderInterfaceFactory::class)->create()->loadByIncrementId('100000001');

$orderService = ObjectManager::getInstance()->create(
    InvoiceManagementInterface::class
);
$invoice = $orderService->prepareInvoice($order);
/** To allow invoice cancelling it should be created without capturing. */
$invoice->setRequestedCaptureCase(Invoice::NOT_CAPTURE)->register();
$order = $invoice->getOrder();
$order->setIsInProcess(true);
$transactionSave = Bootstrap::getObjectManager()
    ->create(Transaction::class);
$transactionSave->addObject($invoice)->addObject($order)->save();
