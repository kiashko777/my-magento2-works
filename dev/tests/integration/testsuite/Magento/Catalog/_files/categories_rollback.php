<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

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
$productsToDelete = ['simple', '12345', 'simple-3', 'simple-4'];

foreach ($productsToDelete as $sku) {
    try {
        $product = $productRepository->get($sku, false, null, true);
        $productRepository->delete($product);
    } catch (NoSuchEntityException $e) {
        //Products already removed
    }
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
