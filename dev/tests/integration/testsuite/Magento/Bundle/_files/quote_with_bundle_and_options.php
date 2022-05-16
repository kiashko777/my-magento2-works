<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\Bundle\Model\Option;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Bundle/_files/product_with_multiple_options.php');

$objectManager = Bootstrap::getObjectManager();

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->load(3);

/** @var $typeInstance \Magento\Bundle\Model\Product\Type */
$typeInstance = $product->getTypeInstance();
$typeInstance->setStoreFilter($product->getStoreId(), $product);
$optionCollection = $typeInstance->getOptionsCollection($product);

$bundleOptions = [];
$bundleOptionsQty = [];
foreach ($optionCollection as $option) {
    /** @var $option Option */
    $selectionsCollection = $typeInstance->getSelectionsCollection([$option->getId()], $product);
    if ($option->isMultiSelection()) {
        $bundleOptions[$option->getId()] = array_column($selectionsCollection->toArray(), 'selection_id');
    } else {
        $bundleOptions[$option->getId()] = $selectionsCollection->getFirstItem()->getSelectionId();
    }
    $bundleOptionsQty[$option->getId()] = 1;
}

$requestInfo = new DataObject(
    [
        'product' => $product->getId(),
        'bundle_option' => $bundleOptions,
        'bundle_option_qty' => $bundleOptionsQty,
        'qty' => 1,
    ]
);

/** @var $cart Cart */
$cart = Bootstrap::getObjectManager()->create(Cart::class);
$cart->addProduct($product, $requestInfo);
$cart->getQuote()->setReservedOrderId('test_cart_with_bundle_and_options');
$cart->save();

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = Bootstrap::getObjectManager()
    ->create(QuoteIdMaskFactory::class)
    ->create();
$quoteIdMask->setQuoteId($cart->getQuote()->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();
$objectManager->removeSharedInstance(Session::class);
