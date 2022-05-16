<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/*
 * Since the bundle product creation GUI doesn't allow to choose values for bundled products' custom options,
 * bundled items should not contain products with required custom options.
 * However, if to create such a bundle product, it will be always out of stock.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture(
    'Magento/Catalog/_files/simple_products_not_visible_individually_rollback.php'
);

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);
try {
    $product = $productRepository->get('bundle-product-1', false, null, true);
    $productRepository->delete($product);
} catch (NoSuchEntityException $e) {
    //Products already removed
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
