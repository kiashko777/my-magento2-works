<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$prices = [5, 10, 15, 20, 50, 100, 150];

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()->create(
    ProductRepositoryInterface::class
);

/** @var $product Product */
$lastProductId = 0;
foreach ($prices as $price) {
    /** @var Product $product */
    $product = Bootstrap::getObjectManager()->create(
        Product::class
    );
    $productId = $lastProductId + 1;
    try {
        $product = $productRepository->get('simple-' . $productId, false, null, true);
        $productRepository->delete($product);
    } catch (NoSuchEntityException $e) {
        //Products already removed
    }

    $lastProductId++;
}

/** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
$collection = Bootstrap::getObjectManager()
    ->create(Collection::class);
$collection
    ->addAttributeToFilter('level', 2)
    ->load()
    ->delete();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
