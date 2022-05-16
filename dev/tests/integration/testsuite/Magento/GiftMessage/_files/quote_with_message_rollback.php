<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\GiftMessage\Model\Message;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
$quote->load('message_order_21', 'reserved_order_id');

/** @var Message $message */
$message = $objectManager->create(Message::class);
$message->load($quote->getGiftMessageId());
$message->delete();

$quote->delete();
