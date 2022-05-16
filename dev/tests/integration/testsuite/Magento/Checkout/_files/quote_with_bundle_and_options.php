<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Bundle\Model\Option;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Bundle/_files/product_with_multiple_options.php');

$objectManager = Bootstrap::getObjectManager();

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->load(3);

/** @var $typeInstance \Magento\Bundle\Model\Product\Type */
//Load options
$typeInstance = $product->getTypeInstance();
$typeInstance->setStoreFilter($product->getStoreId(), $product);
$optionCollection = $typeInstance->getOptionsCollection($product);

$bundleOptions = [];
$bundleOptionsQty = [];
/** @var $option Option */
foreach ($optionCollection as $option) {
    $selectionsCollection = $typeInstance->getSelectionsCollection([$option->getId()], $product);
    if ($option->isMultiSelection()) {
        $bundleOptions[$option->getId()] = array_column($selectionsCollection->toArray(), 'selection_id');
    } else {
        $bundleOptions[$option->getId()] = $selectionsCollection->getFirstItem()->getSelectionId();
    }
    $bundleOptionsQty[$option->getId()] = 1;
}

$requestInfo = new DataObject(
    ['qty' => 1, 'bundle_option' => $bundleOptions, 'bundle_option_qty' => $bundleOptionsQty]
);

/** @var $quote Quote $quote */
$quote = $objectManager->create(Quote::class);
$quote->setStoreId(1)->setReservedOrderId('test_order_bundle')->save();
$quote->addProduct($product, $requestInfo);
$quote->save();
