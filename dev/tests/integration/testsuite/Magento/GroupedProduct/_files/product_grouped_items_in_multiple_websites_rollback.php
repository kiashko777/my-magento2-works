<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

/**
 * @var ProductRepositoryInterface $productRepository
 */
$productRepository = Bootstrap::getObjectManager()->get(
    ProductRepositoryInterface::class
);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
try {
    $productRepository->deleteById('simple');
} catch (NoSuchEntityException $e) {
    //already deleted
}

try {
    $productRepository->deleteById('virtual-product');
} catch (NoSuchEntityException $e) {
    //already deleted
}

try {
    $productRepository->deleteById('grouped-product');
} catch (NoSuchEntityException $e) {
    //already deleted
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
