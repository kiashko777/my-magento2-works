<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_for_second_store_rollback.php');
Resolver::getInstance()->requireDataFixture(
    'Magento/Catalog/_files/product_simple_out_of_stock_without_categories_rollback.php'
);

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$objectManager = Bootstrap::getObjectManager();
/** @var CustomerRegistry $customerRegistry */
$customerRegistry = Bootstrap::getObjectManager()->create(CustomerRegistry::class);
$customer = $customerRegistry->remove(1);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
try {
    $product = $productRepository->deleteById('simple');
} catch (Exception $e) {
    // product already removed
}
/** @var Magento\Store\Model\Store $store */
$store = Bootstrap::getObjectManager()->create(Store::class);
$store->load('fixture_second_store');
if ($store->getId()) {
    $store->delete();
}
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
