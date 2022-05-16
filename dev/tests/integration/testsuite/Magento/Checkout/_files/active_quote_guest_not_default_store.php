<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/second_store.php');

$objectManager = Bootstrap::getObjectManager();
/** @var StoreRepositoryInterface $storeRepository */
$storeRepository = $objectManager->get(StoreRepositoryInterface::class);
$store = $storeRepository->get('fixture_second_store');
$quote = $objectManager->create(Quote::class);
$quote->setStoreId($store->getId())
    ->setIsActive(true)
    ->setIsMultiShipping(false)
    ->setReservedOrderId('test_order_1_not_default_store_guest')
    ->save();

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = $objectManager
    ->create(QuoteIdMaskFactory::class)
    ->create();
$quoteIdMask->setQuoteId($quote->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();
