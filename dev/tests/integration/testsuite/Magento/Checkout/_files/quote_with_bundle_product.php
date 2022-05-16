<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Bundle\Model\Option;
use Magento\Bundle\Model\Selection;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Bundle/_files/product.php');

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
/** @var $product Product */
$product = $productRepository->get('bundle-product');

/** @var $typeInstance \Magento\Bundle\Model\Product\Type */
//Load options
$typeInstance = $product->getTypeInstance();
$typeInstance->setStoreFilter($product->getStoreId(), $product);
$optionCollection = $typeInstance->getOptionsCollection($product);
$selectionCollection = $typeInstance->getSelectionsCollection($typeInstance->getOptionsIds($product), $product);

$bundleOptions = [];
$bundleOptionsQty = [];
/** @var $option Option */
foreach ($optionCollection as $option) {
    /** @var $selection Selection */
    $selection = $selectionCollection->getFirstItem();
    $bundleOptions[$option->getId()] = $selection->getSelectionId();
    $bundleOptionsQty[$option->getId()] = 1;
}

$requestInfo = new DataObject(
    ['qty' => 1, 'bundle_option' => $bundleOptions, 'bundle_option_qty' => $bundleOptionsQty]
);

/** @var $cart Cart */
$cart = Bootstrap::getObjectManager()->create(Cart::class);
$cart->addProduct($product, $requestInfo);
$cart->getQuote()->setReservedOrderId('test_cart_with_bundle');
$cart->save();

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();
$objectManager->removeSharedInstance(Session::class);
