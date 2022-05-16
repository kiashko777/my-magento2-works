<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var CategorySetup $installer */

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\TestFramework\Helper\Bootstrap;

$installer = Bootstrap::getObjectManager()->create(
    CategorySetup::class
);
/**
 * After installation system has two categories: root one with ID:1 and Default category with ID:2
 */
/** @var $category Category */
$category = Bootstrap::getObjectManager()->create(Category::class);
$category->isObjectNew(true);
$category->setId(
    9
)->setName(
    'Category 9'
)->setParentId(
    2
)->setPath(
    '1/2/3'
)->setLevel(
    2
)->setAvailableSortBy(
    'name'
)->setDefaultSortBy(
    'name'
)->setIsActive(
    true
)->setPosition(
    1
)->save();

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setId(
    1
)->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setAttributeSetId(
    $installer->getAttributeSetId('catalog_product', 'Default')
)->setStoreId(
    1
)->setWebsiteIds(
    [1]
)->setName(
    'Simple Products One'
)->setSku(
    'simple'
)->setPrice(
    10
)->setWeight(
    18
)->setStockData(
    [
        'use_config_manage_stock' => 1,
        'qty' => 100,
        'is_qty_decimal' => 0,
        'is_in_stock' => 1,
    ]
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->save();
$objectManager = Bootstrap::getObjectManager();
/** @var CategoryLinkManagementInterface $categoryLinkManagement */
$categoryLinkManagement = $objectManager->create(CategoryLinkManagementInterface::class);
$categoryLinkManagement->assignProductToCategories('simple', [9]);
