<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Registry $registry */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->create(ProductRepositoryInterface::class);

try {
    $product = $productRepository->get('simple', false, null, true);
    $productRepository->delete($product);
} catch (NoSuchEntityException $exception) {
    //Products already removed
}

/** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
$collection = Bootstrap::getObjectManager()
    ->create(Collection::class);
$collection
    ->addAttributeToFilter('name', ['in' => ['Old Root', 'Category 2', 'Category 1']])
    ->load()
    ->delete();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
