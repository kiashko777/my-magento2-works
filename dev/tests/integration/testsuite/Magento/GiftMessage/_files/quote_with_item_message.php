<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\GiftMessage\Model\Message;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Checkout/_files/quote_with_address.php');

$objectManager = Bootstrap::getObjectManager();
/** @var QuoteFactory $quoteFactory */
$quoteFactory = Bootstrap::getObjectManager()->get(QuoteFactory::class);
/** @var QuoteResource $quoteResource */
$quoteResource = Bootstrap::getObjectManager()->get(QuoteResource::class);
$quote = $quoteFactory->create();
$quoteResource->load($quote, 'test_order_1', 'reserved_order_id');
$product = $objectManager->create(Product::class);
$product->setTypeId(
    'simple'
)->setAttributeSetId(
    4
)->setWebsiteIds(
    [1]
)->setName(
    'Simple Products With Message'
)->setSku(
    'simple_with_message'
)->setPrice(
    10
)->setMetaTitle(
    'meta title'
)->setMetaKeyword(
    'meta keyword'
)->setMetaDescription(
    'meta description'
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->setStockData(
    ['use_config_manage_stock' => 0]
)->save();
$quoteProduct = $product->load($product->getIdBySku('simple_with_message'));
$quote->setReservedOrderId('test_order_item_with_message')
    ->addProduct($product->load($product->getIdBySku('simple_with_message')), 1);
$quote->collectTotals()->save();

/** @var Message $message */
$message = $objectManager->create(Message::class);
$message->setSender('John Doe');
$message->setRecipient('Jane Roe');
$message->setMessage('Gift Message Text');
$message->save();
$quote->getItemByProduct($quoteProduct)->setGiftMessageId($message->getId())->save();

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = Bootstrap::getObjectManager()
    ->create(QuoteIdMaskFactory::class)
    ->create();
$quoteIdMask->setQuoteId($quote->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();
