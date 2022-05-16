<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var Registry $registry */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/**
 * @var Magento\Catalog\Api\ProductRepositoryInterface $productRepository
 */
$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);
try {
    $product = $productRepository->get('wrong-simple', false, null, true);
    $productRepository->delete($product);
} catch (NoSuchEntityException $e) {
    //Products already removed
}

try {
    $customDesignProduct = $productRepository->get('simple-156', false, null, true);
    $productRepository->delete($customDesignProduct);
} catch (NoSuchEntityException $e) {
    //Products already removed
}

try {
    $customDesignProduct = $productRepository->get('simple-249', false, null, true);
    $productRepository->delete($customDesignProduct);
} catch (NoSuchEntityException $e) {
    //Products already removed
}


$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
