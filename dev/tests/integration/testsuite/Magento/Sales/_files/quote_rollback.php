<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Registry $registry */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\StockRegistryStorage;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $quote Quote */
$quote = Bootstrap::getObjectManager()->create(Quote::class);
$quote->load('test01', 'reserved_order_id');
if ($quote->getId()) {
    $quote->delete();
}

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->create(ProductRepositoryInterface::class);

try {
    $product = $productRepository->get('simple', false, null, true);
    $productRepository->delete($product);
} catch (NoSuchEntityException $exception) {
    //Products already removed
}

/** @var StockRegistryStorage $stockRegistryStorage */
$stockRegistryStorage = Bootstrap::getObjectManager()
    ->get(StockRegistryStorage::class);
$stockRegistryStorage->removeStockItem(1);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
