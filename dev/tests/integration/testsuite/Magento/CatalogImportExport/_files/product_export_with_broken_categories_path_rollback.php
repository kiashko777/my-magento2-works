<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

// Remove products
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

try {
    $product = $productRepository->get('simple', false, null, true);
    $productRepository->delete($product);
} catch (NoSuchEntityException $exception) {
    //Products already removed
}

$productCollection = Bootstrap::getObjectManager()
    ->get(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
foreach ($productCollection as $product) {
    $product->delete();
}

//Remove categories
/** @var Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
$collection = $objectManager->create(Collection::class);
foreach ($collection->addAttributeToFilter('level', ['in' => [2, 3, 4]]) as $category) {
    /** @var Category $category */
    $category->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
