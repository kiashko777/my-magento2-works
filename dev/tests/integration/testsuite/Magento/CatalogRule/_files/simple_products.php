<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Action;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Store\Model\StoreManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/CatalogRule/_files/attribute.php');

$objectManager = Bootstrap::getObjectManager();

$storeManager = $objectManager->get(StoreManager::class);
$store = $storeManager->getStore('default');
$productRepository = $objectManager->get(ProductRepositoryInterface::class);

$installer = $objectManager->get(CategorySetup::class);
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');

$product = $objectManager->create(Product::class)
    ->setTypeId('simple')
    ->setId(1)
    ->setAttributeSetId($attributeSetId)
    ->setWebsiteIds([1])
    ->setName('Simple Products 1')
    ->setSku('simple1')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData([
        'use_config_manage_stock' => 1,
        'qty' => 100,
        'is_qty_decimal' => 0,
        'is_in_stock' => 1,
    ]);
$productRepository->save($product);
$productAction = $objectManager->get(Action::class);
$productAction->updateAttributes([$product->getId()], ['test_attribute' => 'test_attribute_value'], $store->getId());

$product = $objectManager->create(Product::class)
    ->setTypeId('simple')
    ->setId(2)
    ->setAttributeSetId($attributeSetId)
    ->setWebsiteIds([1])
    ->setName('Simple Products 2')
    ->setSku('simple2')
    ->setPrice(9.9)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData([
        'use_config_manage_stock' => 1,
        'qty' => 100,
        'is_qty_decimal' => 0,
        'is_in_stock' => 1,
    ]);
$productRepository->save($product);
