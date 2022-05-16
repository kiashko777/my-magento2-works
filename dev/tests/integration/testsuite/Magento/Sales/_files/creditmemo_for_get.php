<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo\Item;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/default_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');

$objectManager = Bootstrap::getObjectManager();
/** @var Order $order */
$order = $objectManager->create(Order::class);
$order->loadByIncrementId('100000001');

/** @var CreditmemoFactory $creditmemoFactory */
$creditmemoFactory = $objectManager->get(CreditmemoFactory::class);
$creditmemo = $creditmemoFactory->createByOrder($order, $order->getData());
$creditmemo->setOrder($order);
$creditmemo->setState(Magento\Sales\Model\Order\Creditmemo::STATE_OPEN);
$creditmemo->setIncrementId('100000001');
$creditmemo->save();

/** @var \Magento\Sales\Model\Order\Item $orderItem */
$orderItem = current($order->getAllItems());
$orderItem->setName('Test item')
    ->setQtyRefunded(1)
    ->setQtyInvoiced(10)
    ->setOriginalPrice(20);

/** @var Item $creditItem */
$creditItem = $objectManager->get(Item::class);
$creditItem->setCreditmemo($creditmemo)
    ->setName('Creditmemo item')
    ->setOrderItemId($orderItem->getId())
    ->setQty(1)
    ->setPrice(20)
    ->save();
