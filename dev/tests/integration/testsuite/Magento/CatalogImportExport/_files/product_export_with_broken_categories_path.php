<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\CategoryLinkRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$defaultAttributeSet = $objectManager->get(Magento\Eav\Model\Config::class)
    ->getEntityType('catalog_product')
    ->getDefaultAttributeSetId();

$productRepository = $objectManager->create(
    ProductRepositoryInterface::class
);

$categoryLinkRepository = $objectManager->create(
    CategoryLinkRepositoryInterface::class,
    [
        'productRepository' => $productRepository
    ]
);

/** @var Magento\Catalog\Api\CategoryLinkManagementInterface $linkManagement */
$categoryLinkManagement = $objectManager->create(CategoryLinkManagementInterface::class);
$reflectionClass = new ReflectionClass(get_class($categoryLinkManagement));
$properties = [
    'productRepository' => $productRepository,
    'categoryLinkRepository' => $categoryLinkRepository
];
foreach ($properties as $key => $value) {
    if ($reflectionClass->hasProperty($key)) {
        $reflectionProperty = $reflectionClass->getProperty($key);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($categoryLinkManagement, $value);
    }
}

/**
 * After installation system has two categories: root one with ID:1 and Default category with ID:2
 */
/** @var $category Category */
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

$category = $objectManager->create(Category::class);
$category->isObjectNew(true);
$category->setId(5)
    ->setName('Category 1.1')
    ->setParentId(3)
    ->setPath('1/2/3/4/5')
    ->setLevel(4)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setIsAnchor(true)
    ->setPosition(1)
    ->save();

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId($defaultAttributeSet)
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

$categoryLinkManagement->assignProductToCategories(
    $product->getSku(),
    [5]
);
