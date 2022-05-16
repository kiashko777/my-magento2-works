<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
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
    /** @var $simpleProduct Product */
    $simpleProduct = $productRepository->get('simple', false, null, true);
    $simpleProduct->delete();
} catch (NoSuchEntityException $e) {
    //already deleted
}

try {
    /** @var $virtualProduct Product */
    $virtualProduct = $productRepository->get('virtual-product', false, null, true);
    $virtualProduct->delete();
} catch (NoSuchEntityException $e) {
    //already deleted
}

try {
    /** @var $groupedProduct Product */
    $groupedProduct = $productRepository->get('grouped-product', false, null, true);
    $groupedProduct->delete();
} catch (NoSuchEntityException $e) {
    //already deleted
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
