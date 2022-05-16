<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var ObjectManager $objectManager */

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Option\Repository;
use Magento\Catalog\Model\Product\Option\SaveHandler;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductRepository;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$objectManager = Bootstrap::getObjectManager();

$objectManager->removeSharedInstance(ProductRepository::class);
$objectManager->removeSharedInstance(Repository::class);
$objectManager->removeSharedInstance(SaveHandler::class);

$productRepository = $objectManager->get(ProductRepository::class);

/** @var $product Product */
$product = $objectManager->create(Product::class);

$product->setTypeId(
    'simple'
)->setAttributeSetId(
    4
)->setWebsiteIds(
    [1]
)->setName(
    'Simple Products With Custom Options'
)->setSku(
    'simple_dropdown_option'
)->setPrice(
    200
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
        'qty' => 0,
        'is_in_stock' => 0,
        'manage_stock' => 1,
    ]
);

$options = [
    [
        'title' => 'drop_down option',
        'type' => 'drop_down',
        'is_require' => true,
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
                'price_type' => 'percent',
                'sku' => 'drop_down option 2 sku',
                'sort_order' => 2,
            ],
        ],
    ]
];

$customOptions = [];

/** @var ProductCustomOptionInterfaceFactory $customOptionFactory */
$customOptionFactory = $objectManager->create(ProductCustomOptionInterfaceFactory::class);
$optionValueFactory = $objectManager->create(
    ProductCustomOptionValuesInterfaceFactory::class
);

foreach ($options as $option) {
    /** @var ProductCustomOptionInterface $customOption */
    $customOption = $customOptionFactory->create(['data' => $option]);
    $customOption->setProductSku($product->getSku());
    if (isset($option['values'])) {
        $values = [];
        foreach ($option['values'] as $value) {
            $value = $optionValueFactory->create(['data' => $value]);
            $values[] = $value;
        }
        $customOption->setValues($values);
    }
    $customOptions[] = $customOption;
}

$product->setOptions($customOptions);
$product->save();
