<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$categories = [
    [
        'id' => 444,
        'parentId' => 2,
        'level' => 2,
        'path' => '1/2/3'
    ],
    [
        'id' => 445,
        'parentId' => 444,
        'level' => 3,
        'path' => '1/2/3/4'
    ],
    [
        'id' => 446,
        'parentId' => 445,
        'level' => 4,
        'path' => '1/2/3/4/5'
    ],
];

$products = [
    [
        'id' => 444,
        'categoryIDs' => [446]
    ],
    [
        'id' => 445,
        'categoryIDs' => [446]
    ]
];

foreach ($categories as $category) {
    $categoryModel = Bootstrap::getObjectManager()
        ->create(Category::class);
    $categoryModel->isObjectNew(true);
    $categoryModel->setId($category['id'])
        ->setName('Category ' . $category['id'])
        ->setParentId($category['parentId'])
        ->setPath($category['path'])
        ->setLevel($category['level'])
        ->setAvailableSortBy('name')
        ->setDefaultSortBy('name')
        ->setIsActive(true)
        ->setPosition(1)
        ->setAvailableSortBy(['position'])
        ->save();
}

foreach ($products as $product) {
    /** @var $product Product */
    $productModel = Bootstrap::getObjectManager()
        ->create(Product::class);
    $productModel->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
        ->setId($product['id'])
        ->setAttributeSetId(4)
        ->setStoreId(1)
        ->setWebsiteIds([1])
        ->setName('Simple Products ' . $product['id'])
        ->setSku('simple' . $product['id'])
        ->setPrice(10)
        ->setWeight(18)
        ->setStockData(['use_config_manage_stock' => 0])
        ->setCategoryIds($product['categoryIDs'])
        ->setVisibility(Visibility::VISIBILITY_BOTH)
        ->setStatus(Status::STATUS_ENABLED)
        ->save();
}
