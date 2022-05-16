<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Magento\Framework\ObjectManagerInterface $objectManager */
$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var ProductAttributeRepositoryInterface $attributeRepository */
$attributeRepository = $objectManager->get(ProductAttributeRepositoryInterface::class);
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

try {
    /** @var ProductInterface $product */
    $product = $productRepository->get('simple_with_custom_flat_attribute');
    $productRepository->delete($product);
} catch (NoSuchEntityException $e) {
}

try {
    /** @var ProductAttributeInterface $attribute */
    $attribute = $attributeRepository->get('flat_attribute');
    $attributeRepository->delete($attribute);
} catch (NoSuchEntityException $e) {
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
