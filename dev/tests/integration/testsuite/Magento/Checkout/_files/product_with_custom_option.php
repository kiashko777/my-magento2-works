<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Checkout/_files/simple_product.php');

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->load(1);

/** @var $product Product */
$product->setCanSaveCustomOptions(
    true
)->setHasOptions(
    true
);

$oldOptions = [
    [
        'id' => 1,
        'option_id' => 0,
        'previous_group' => 'text',
        'title' => 'Test Field',
        'type' => 'field',
        'is_require' => 1,
        'sort_order' => 0,
        'price' => 1,
        'price_type' => 'fixed',
        'sku' => '1-text',
        'max_characters' => 100,
    ],
];

$customOptions = [];

/** @var ProductCustomOptionInterfaceFactory $customOptionFactory */
$customOptionFactory = $objectManager->create(ProductCustomOptionInterfaceFactory::class);

foreach ($oldOptions as $option) {
    /** @var ProductCustomOptionInterface $customOption */
    $customOption = $customOptionFactory->create(['data' => $option]);
    $customOption->setProductSku($product->getSku());

    $customOptions[] = $customOption;
}

$product->setOptions($customOptions)->save();

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->load(1);
$optionId = $product->getOptions()[0]->getOptionId();

$requestInfo = new DataObject(['qty' => 1, 'options' => [$optionId => 'test']]);

Resolver::getInstance()->requireDataFixture('Magento/Checkout/_files/cart.php');
