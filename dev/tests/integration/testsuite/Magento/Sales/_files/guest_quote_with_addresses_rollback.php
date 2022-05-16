<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var Registry $registry */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\StockRegistryStorage;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $quote Quote */
$quote = $objectManager->create(Quote::class);
/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = $objectManager->create(QuoteIdMask::class);

$quote->load('guest_quote', 'reserved_order_id');

$quoteId = $quote->getId();
if (null !== $quoteId) {
    $quote->delete();
    $quoteIdMask->delete($quoteId);
}

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);

try {
    $product = $productRepository->get('simple-product-guest-quote', false, null, true);
    $productRepository->delete($product);
} catch (NoSuchEntityException $exception) {
    //Products already removed
}

/** @var StockRegistryStorage $stockRegistryStorage */
$stockRegistryStorage = Bootstrap::getObjectManager()
    ->get(StockRegistryStorage::class);
$stockRegistryStorage->clean();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
