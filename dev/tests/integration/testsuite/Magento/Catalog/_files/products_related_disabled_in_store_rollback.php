<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
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

try {
    $firstProduct = $productRepository->get('simple', false, null, true);
    $productRepository->delete($firstProduct);
} catch (NoSuchEntityException $exception) {
    //Products already removed
}

try {
    $secondProduct = $productRepository->get('simple_with_related', false, null, true);
    $productRepository->delete($secondProduct);
} catch (NoSuchEntityException $exception) {
    //Products already removed
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
