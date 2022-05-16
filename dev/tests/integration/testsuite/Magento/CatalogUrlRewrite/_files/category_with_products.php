<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\CategoryLinkRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$installer = $objectManager->create(CategorySetup::class);

Bootstrap::getInstance()->loadArea(
    FrontNameResolver::AREA_CODE
);

$category = $objectManager->create(Category::class);
$category->isObjectNew(true);
$category->setId(3)
    ->setName('Category 1')
    ->setParentId(2)
    ->setPath('1/2/3')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$categoryLinkRepository = $objectManager->create(
    CategoryLinkRepositoryInterface::class,
    [
        'productRepository' => $productRepository
    ]
);
$categoryLinkManagement = $objectManager->create(
    CategoryLinkManagementInterface::class,
    [
        'productRepository' => $productRepository,
        'categoryLinkRepository' => $categoryLinkRepository
    ]
);

$product = $objectManager->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
    ->setStoreId(1)
    ->setWebsiteIds([1])
    ->setName('Simple Products')
    ->setSku('simple')
    ->setPrice(10)
    ->setWeight(18)
    ->setStockData(['use_config_manage_stock' => 0])
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->save();
$categoryLinkManagement->assignProductToCategories($product->getSku(), [3]);

$product = $objectManager->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
    ->setStoreId(1)
    ->setWebsiteIds([1])
    ->setName('Simple Products Two')
    ->setSku('12345') // SKU intentionally contains digits only
    ->setPrice(45.67)
    ->setWeight(56)
    ->setStockData(['use_config_manage_stock' => 0])
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->save();
$categoryLinkManagement->assignProductToCategories($product->getSku(), [3]);
