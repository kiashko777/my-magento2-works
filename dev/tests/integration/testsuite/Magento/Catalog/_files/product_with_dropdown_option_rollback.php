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

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$productRepository = Bootstrap::getObjectManager()
    ->create(ProductRepositoryInterface::class);
try {
    $product = $productRepository->get('simple_dropdown_option', false, null, true);
    $product->delete();
} catch (NoSuchEntityException $e) {
}
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
