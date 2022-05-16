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
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/**
 * @var Magento\Catalog\Api\ProductRepositoryInterface $productRepository
 */
$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);
try {
    $product = $productRepository->get('simple', false, null, true);
    $productRepository->delete($product);
} catch (NoSuchEntityException $e) {
    //Products already removed
}

try {
    $customDesignProduct = $productRepository->get('custom-design-simple-product', false, null, true);
    $productRepository->delete($customDesignProduct);
} catch (NoSuchEntityException $e) {
    //Products already removed
}

/** @var StockRegistryStorage $stockRegistryStorage */
$stockRegistryStorage = Bootstrap::getObjectManager()
    ->get(StockRegistryStorage::class);
$stockRegistryStorage->clean();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
