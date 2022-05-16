<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

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

try {
    $productRepository = Bootstrap::getObjectManager()
        ->get(ProductRepositoryInterface::class);
    $product = $productRepository->get('product-with-xss', true);
    if ($product->getId()) {
        $productRepository->delete($product);
    }
} catch (NoSuchEntityException $e) {
    //Products already removed
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
