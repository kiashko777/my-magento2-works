<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var ObjectManagerInterface $objectManager */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

foreach (['simple 01', 'simple 02', 'simple 03'] as $sku) {
    try {
        $product = $productRepository->get($sku, false, null, true);
        $productRepository->delete($product);
    } catch (NoSuchEntityException $exception) {
        //Products already removed
    }
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
