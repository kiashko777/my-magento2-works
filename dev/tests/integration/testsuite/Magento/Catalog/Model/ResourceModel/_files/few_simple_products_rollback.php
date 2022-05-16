<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);

/**
 * Delete 10 products
 */
$productsAmount = 10;

try {
    for ($i = 1; $i <= $productsAmount; $i++) {
        /** @var ProductInterface $product */
        $product = $productRepository->get("Products{$i}", false, null, true);
        $productRepository->delete($product);
    }
} catch (NoSuchEntityException $e) {
}
