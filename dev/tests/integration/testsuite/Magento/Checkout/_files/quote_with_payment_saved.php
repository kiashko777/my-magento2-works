<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Checkout/_files/quote_with_address.php');

$objectManager = Bootstrap::getObjectManager();
/** @var QuoteFactory $quoteFactory */
$quoteFactory = $objectManager->get(QuoteFactory::class);
/** @var QuoteResource $quoteResource */
$quoteResource = $objectManager->get(QuoteResource::class);
$quote = $quoteFactory->create();
$quoteResource->load($quote, 'test_order_1', 'reserved_order_id');

/** @var Json $serializer */
$serializer = $objectManager->create(Json::class);

$quote->setReservedOrderId(
    'test_order_1_with_payment'
);

$paymentDetails = [
    'transaction_id' => 100500,
    'consumer_key' => '123123q',
];

$quote->getPayment()
    ->setMethod('checkmo')
    ->setPoNumber('poNumber')
    ->setCcOwner('tester')
    ->setCcType('visa')
    ->setCcExpYear(2014)
    ->setCcExpMonth(1)
    ->setAdditionalData($serializer->serialize($paymentDetails));

$quote->collectTotals()->save();

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = $objectManager
    ->create(QuoteIdMaskFactory::class)
    ->create();
$quoteIdMask->setQuoteId($quote->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();
