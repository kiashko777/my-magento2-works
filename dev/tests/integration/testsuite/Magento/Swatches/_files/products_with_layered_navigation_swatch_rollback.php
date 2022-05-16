<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

foreach (['simple1', 'simple2', 'simple3'] as $sku) {
    try {
        $product = $productRepository->get($sku, false, null, true);
        $productRepository->delete($product);
    } catch (NoSuchEntityException $exception) {
        //Products already removed
    }
}

$productCollection = Bootstrap::getObjectManager()
    ->get(Collection::class);
foreach ($productCollection as $product) {
    $product->delete();
}

/** @var $category Category */
$category = Bootstrap::getObjectManager()->create(Category::class);
$category->load(333);
if ($category->getId()) {
    $category->delete();
}

/** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
$attribute = Bootstrap::getObjectManager()
    ->create(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);

$attribute->loadByCode(4, 'color_swatch');

if ($attribute->getId()) {
    $attribute->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
