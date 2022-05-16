<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\Stock\Status;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);

foreach (['simple_10', 'simple_20', 'configurable', 'simple_30', 'simple_40', 'configurable_12345'] as $sku) {
    try {
        $product = $productRepository->get($sku, true);

        $stockStatus = $objectManager->create(Status::class);
        $stockStatus->load($product->getEntityId(), 'product_id');
        $stockStatus->delete();

        $productRepository->delete($product);
    } catch (NoSuchEntityException $e) {
        //Products already removed
    }
}

Resolver::getInstance()->requireDataFixture(
    'Magento/ConfigurableProduct/_files/configurable_attribute_first_rollback.php'
);
Resolver::getInstance()->requireDataFixture(
    'Magento/ConfigurableProduct/_files/configurable_attribute_second_rollback.php'
);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
