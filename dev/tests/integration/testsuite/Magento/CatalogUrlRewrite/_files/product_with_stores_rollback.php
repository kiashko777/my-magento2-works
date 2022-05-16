<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Bootstrap::getInstance()->getInstance()->reinitialize();

Resolver::getInstance()->requireDataFixture('Magento/CatalogUrlRewrite/_files/categories_with_stores_rollback.php');

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);
try {
    $product = $productRepository->get('simple', true);
    if ($product->getId()) {
        $productRepository->delete($product);
    }
} catch (NoSuchEntityException $e) {
}
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
