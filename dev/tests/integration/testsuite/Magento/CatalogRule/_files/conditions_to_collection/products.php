<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$productRepository = $objectManager->create(ProductRepositoryInterface::class);

$attributeSetMuffins = $objectManager->create(Set::class)
    ->load('Super Powerful Muffins', 'attribute_set_name');
$attributeSetRangers = $objectManager->create(Set::class)
    ->load('Banana Rangers', 'attribute_set_name');
$attributeSetGuardians = $objectManager->create(Set::class)
    ->load('Guardians of the Refrigerator', 'attribute_set_name');

$productsData = [
    [
        'type-id' => 'simple',
        'attribute-set-id' => $attributeSetMuffins->getId(),
        'website-ids' => [1],
        'name' => 'Simple Products 1 (Sale)',
        'sku' => 'simple-product-1',
        'price' => 10,
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock-data' => ['use_config_manage_stock' => 1, 'qty' => 77, 'is_in_stock' => 1],
        'qty' => 42,
        'categories' => ['Category 1', 'Category 2'],
    ],
    [
        'type-id' => 'simple',
        'attribute-set-id' => $attributeSetRangers->getId(),
        'website-ids' => [1],
        'name' => 'Simple Products 2',
        'sku' => 'simple-product-2',
        'price' => 10,
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock-data' => ['use_config_manage_stock' => 1, 'qty' => 22, 'is_in_stock' => 1],
        'qty' => 42,
        'categories' => ['Category 1', 'Category 2'],
    ],
    [
        'type-id' => 'simple',
        'attribute-set-id' => $attributeSetGuardians->getId(),
        'website-ids' => [1],
        'name' => 'Simple Products 3',
        'sku' => 'simple-product-3',
        'price' => 10,
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock-data' => ['use_config_manage_stock' => 1, 'qty' => 100, 'is_in_stock' => 1],
        'qty' => 42,
        'categories' => ['Category 1.1', 'Category 3'],
    ],
    [
        'type-id' => 'simple',
        'attribute-set-id' => $attributeSetMuffins->getId(),
        'website-ids' => [1],
        'name' => 'Simple Products 4',
        'sku' => 'simple-product-4',
        'price' => 10,
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock-data' => ['use_config_manage_stock' => 1, 'qty' => 22, 'is_in_stock' => 1],
        'qty' => 42,
        'categories' => ['Category 1.1', 'Category 3'],
    ],
    [
        'type-id' => 'simple',
        'attribute-set-id' => $attributeSetRangers->getId(),
        'website-ids' => [1],
        'name' => 'Simple Products 5',
        'sku' => 'simple-product-5',
        'price' => 10,
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock-data' => ['use_config_manage_stock' => 1, 'qty' => 22, 'is_in_stock' => 1],
        'qty' => 42,
        'categories' => ['Category 1.2', 'Category 1.1.1'],
    ],
    [
        'type-id' => 'simple',
        'attribute-set-id' => $attributeSetGuardians->getId(),
        'website-ids' => [1],
        'name' => 'Simple Products 6',
        'sku' => 'simple-product-6',
        'price' => 10,
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock-data' => ['use_config_manage_stock' => 1, 'qty' => 97, 'is_in_stock' => 1],
        'qty' => 42,
        'categories' => ['Category 1.2', 'Category 1.1.1'],
    ],
    [
        'type-id' => 'simple',
        'attribute-set-id' => $attributeSetMuffins->getId(),
        'website-ids' => [1],
        'name' => 'Simple Products 7',
        'sku' => 'simple-product-7',
        'price' => 10,
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock-data' => ['use_config_manage_stock' => 1, 'qty' => 22, 'is_in_stock' => 1],
        'qty' => 42,
        'categories' => ['Category 3', 'Category 2'],
    ],
    [
        'type-id' => 'simple',
        'attribute-set-id' => $attributeSetRangers->getId(),
        'website-ids' => [1],
        'name' => 'Simple Products 8',
        'sku' => 'simple-product-8',
        'price' => 10,
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock-data' => ['use_config_manage_stock' => 1, 'qty' => 22, 'is_in_stock' => 1],
        'qty' => 42,
        'categories' => ['Category 3', 'Category 2'],
    ],
    [
        'type-id' => 'simple',
        'attribute-set-id' => $attributeSetGuardians->getId(),
        'website-ids' => [1],
        'name' => 'Simple Products 9 (Sale)',
        'sku' => 'simple-product-9',
        'price' => 10,
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock-data' => ['use_config_manage_stock' => 1, 'qty' => 22, 'is_in_stock' => 1],
        'qty' => 42,
        'categories' => ['Category 1.1', 'Category 1.2'],
    ],
    [
        'type-id' => 'simple',
        'attribute-set-id' => $attributeSetMuffins->getId(),
        'website-ids' => [1],
        'name' => 'Simple Products 10',
        'sku' => 'simple-product-10',
        'price' => 10,
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock-data' => ['use_config_manage_stock' => 1, 'qty' => 22, 'is_in_stock' => 1],
        'qty' => 42,
        'categories' => ['Category 1.1', 'Category 1.2'],
    ],
    [
        'type-id' => 'simple',
        'attribute-set-id' => $attributeSetRangers->getId(),
        'website-ids' => [1],
        'name' => 'Simple Products 11',
        'sku' => 'simple-product-11',
        'price' => 10,
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock-data' => ['use_config_manage_stock' => 1, 'qty' => 22, 'is_in_stock' => 1],
        'qty' => 42,
        'categories' => ['Category 1.1.1'],
    ],
    [
        'type-id' => 'simple',
        'attribute-set-id' => $attributeSetGuardians->getId(),
        'website-ids' => [1],
        'name' => 'Simple Products 12 (Sale)',
        'sku' => 'simple-product-12',
        'price' => 10,
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock-data' => ['use_config_manage_stock' => 1, 'qty' => 22, 'is_in_stock' => 1],
        'qty' => 42,
        'categories' => ['Category 1.1.1'],
    ],
];

foreach ($productsData as $productData) {
    $categoriesIds = [];

    foreach ($productData['categories'] as $category) {
        /** @var Collection $categoryCollection */
        $categoryCollection = $objectManager->create(Collection::class);
        $categoryCollection->addAttributeToFilter('name', $category);

        array_push($categoriesIds, ...$categoryCollection->getAllIds());
    }

    /** @var $product Product */
    $product = Bootstrap::getObjectManager()
        ->create(Product::class);

    $product
        ->setTypeId($productData['type-id'])
        ->setAttributeSetId($productData['attribute-set-id'])
        ->setWebsiteIds($productData['website-ids'])
        ->setName($productData['name'])
        ->setSku($productData['sku'])
        ->setPrice($productData['price'])
        ->setVisibility($productData['visibility'])
        ->setStatus($productData['status'])
        ->setStockData($productData['stock-data'])
        ->setQty($productData['qty'])
        ->setCategoryIds($categoriesIds);

    $productRepository->save($product);
}
