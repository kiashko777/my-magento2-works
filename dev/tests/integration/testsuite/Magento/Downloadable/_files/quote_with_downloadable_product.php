<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Downloadable/_files/product_downloadable.php');

Bootstrap::getInstance()->loadArea('frontend');
$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('downloadable-product');

/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
$quote->setCustomerIsGuest(
    true
)->setStoreId(
    $objectManager->get(
        StoreManagerInterface::class
    )->getStore()->getId()
)->setReservedOrderId(
    'reserved_order_id_1'
)->setIsMultiShipping(
    false
)->addProduct(
    $product,
    new DataObject([
        'links' => array_keys($product->getDownloadableLinks())
    ])
);
$quote->collectTotals();
$quote->save();

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = $objectManager
    ->create(QuoteIdMaskFactory::class)
    ->create();
$quoteIdMask->setQuoteId($quote->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();
