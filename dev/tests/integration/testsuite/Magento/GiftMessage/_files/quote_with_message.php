<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\GiftMessage\Model\Message;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Message $message */
$message = $objectManager->create(Message::class);
$message->setSender('Romeo');
$message->setRecipient('Mercutio');
$message->setMessage('I thought all for the best.');
$message->save();

/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
$quote->setData(
    [
        'store_id' => 1,
        'is_active' => 1,
        'reserved_order_id' => 'message_order_21',
        'gift_message_id' => $message->getId(),
    ]
);
$quote->save();

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = Bootstrap::getObjectManager()
    ->create(QuoteIdMaskFactory::class)
    ->create();
$quoteIdMask->setQuoteId($quote->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();
