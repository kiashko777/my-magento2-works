<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var $product Product */
$product = $objectManager->create(Product::class);

$product->setTypeId(
    'virtual'
)->setAttributeSetId(
    4
)->setWebsiteIds(
    [1]
)->setName(
    'Virtual Products With Custom Options'
)->setSku(
    'virtual'
)->setPrice(
    10
)->setMetaTitle(
    'meta title'
)->setMetaKeyword(
    'meta keyword'
)->setMetaDescription(
    'meta description'
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->setCanSaveCustomOptions(
    true
)->setStockData(
    [
        'qty' => 100,
        'is_in_stock' => 1,
        'manage_stock' => 1,
    ]
)->setHasOptions(true);

$options = [
    [
        'title' => 'test_option_code_1',
        'type' => 'field',
        'is_require' => true,
        'sort_order' => 1,
        'price' => -10.0,
        'price_type' => 'fixed',
        'sku' => 'sku1',
        'max_characters' => 10,
    ],
    [
        'title' => 'area option',
        'type' => 'area',
        'is_require' => true,
        'sort_order' => 2,
        'price' => 20.0,
        'price_type' => 'percent',
        'sku' => 'sku2',
        'max_characters' => 20
    ],
    [
        'title' => 'drop_down option',
        'type' => 'drop_down',
        'is_require' => false,
        'sort_order' => 4,
        'values' => [
            [
                'title' => 'drop_down option 1',
                'price' => 10,
                'price_type' => 'fixed',
                'sku' => 'drop_down option 1 sku',
                'sort_order' => 1,
            ],
            [
                'title' => 'drop_down option 2',
                'price' => 20,
                'price_type' => 'fixed',
                'sku' => 'drop_down option 2 sku',
                'sort_order' => 2,
            ],
        ],
    ],
    [
        'title' => 'multiple option',
        'type' => 'multiple',
        'is_require' => false,
        'sort_order' => 5,
        'values' => [
            [
                'title' => 'multiple option 1',
                'price' => 10,
                'price_type' => 'fixed',
                'sku' => 'multiple option 1 sku',
                'sort_order' => 1,
            ],
            [
                'title' => 'multiple option 2',
                'price' => 20,
                'price_type' => 'fixed',
                'sku' => 'multiple option 2 sku',
                'sort_order' => 2,
            ],
        ],
    ]
];

$customOptions = [];

/** @var ProductCustomOptionInterfaceFactory $customOptionFactory */
$customOptionFactory = $objectManager->get(ProductCustomOptionInterfaceFactory::class);

foreach ($options as $option) {
    /** @var ProductCustomOptionInterface $customOption */
    $customOption = $customOptionFactory->create(['data' => $option]);
    $customOption->setProductSku($product->getSku());

    $customOptions[] = $customOption;
}

$product->setOptions($customOptions);

/** @var ProductRepositoryInterface $productRepositoryFactory */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$productRepository->save($product);
