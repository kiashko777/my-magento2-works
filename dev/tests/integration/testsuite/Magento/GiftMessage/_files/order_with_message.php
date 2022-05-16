<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\GiftMessage\Model\Message;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');

$objectManager = Bootstrap::getObjectManager();

/** @var Message $message */
$message = $objectManager->create(Message::class);
$message->setSender('Romeo');
$message->setRecipient('Mercutio');
$message->setMessage('I thought all for the best.');
$message->save();

/** @var Order $order */
$order = $objectManager->create(Order::class)->loadByIncrementId('100000001');

/** @var OrderItemInterface $orderItem */
$orderItem = $order->getItems();
$orderItem = array_shift($orderItem);
$orderItem->setGiftMessageId($message->getId());

$order->setItems([$orderItem])->setGiftMessageId($message->getId());
$order->save();
