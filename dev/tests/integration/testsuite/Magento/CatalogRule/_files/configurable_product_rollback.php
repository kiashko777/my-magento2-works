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
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Bootstrap::getInstance()->getInstance()->reinitialize();

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
try {
    $product = $productRepository->get('configurable', false, null, true);
    $productRepository->delete($product);
} catch (NoSuchEntityException $e) {
    //Nothing to delete
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

Resolver::getInstance()->requireDataFixture('Magento/ConfigurableProduct/_files/configurable_attribute_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/CatalogRule/_files/simple_products_rollback.php');
