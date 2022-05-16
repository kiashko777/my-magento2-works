<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\StockRegistryStorage;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$productSkuList = ['simple', 'simple_with_upsell'];
foreach ($productSkuList as $sku) {
    try {
        $productRepository = Bootstrap::getObjectManager()
            ->get(ProductRepositoryInterface::class);
        $product = $productRepository->get($sku, true);
        if ($product->getId()) {
            $productRepository->delete($product);
        }
    } catch (NoSuchEntityException $e) {
        //Products already removed
    }
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

/** @var StockRegistryStorage $stockRegistryStorage */
$stockRegistryStorage = Bootstrap::getObjectManager()
    ->get(StockRegistryStorage::class);
$stockRegistryStorage->clean();
