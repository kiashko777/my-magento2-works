<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo\ItemFactory;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/default_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');

$objectManager = Bootstrap::getObjectManager();
/** @var Order $order */
$orderCollection = $objectManager->create(Order::class)->getCollection();
$order = $orderCollection->getFirstItem();

$creditmemoItemFactory = $objectManager->create(ItemFactory::class);
/** @var CreditmemoFactory $creditmemoFactory */
$creditmemoFactory = $objectManager->get(CreditmemoFactory::class);
$creditmemo = $creditmemoFactory->createByOrder($order, $order->getData());
$creditmemo->setOrder($order);
$creditmemo->setState(Magento\Sales\Model\Order\Creditmemo::STATE_OPEN);
foreach ($order->getItems() as $item) {
    $creditmemoItem = $creditmemoItemFactory->create(
        ['data' => [
            'order_item_id' => $item->getId(),
            'sku' => $item->getSku(),
        ],
        ]
    );
    $creditmemo->addItem($creditmemoItem);
}
$creditmemo->save();
