<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\Data\CustomOptionInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\CustomOptions\CustomOptionFactory;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\ProductOptionExtensionFactory;
use Magento\Quote\Api\Data\ProductOptionExtensionInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\ProductOption;
use Magento\Quote\Model\Quote\ProductOptionFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple.php');
Resolver::getInstance()->requireDataFixture('Magento/Checkout/_files/active_quote.php');

$optionValue = [
    'field' => 'Test value',
    'date_time' => [
        'year' => '2015',
        'month' => '9',
        'day' => '9',
        'hour' => '2',
        'minute' => '2',
        'day_part' => 'am',
        'date_internal' => '',
    ],
    'drop_down' => '3-1-select',
    'radio' => '4-1-radio',
];

$objectManager = Bootstrap::getObjectManager();

$productRepository = $objectManager->create(ProductRepositoryInterface::class);
/** @var ProductInterface $product */
$product = $productRepository->get('simple');

/** @var Quote $quote */
$quote = $objectManager->create(Quote::class);
/** @var CartItemRepositoryInterface $quoteItemRepository */
$quoteItemRepository = $objectManager->create(CartItemRepositoryInterface::class);
/** @var CartItemInterface $cartItem */
$cartItem = $objectManager->create(CartItemInterface::class);
/** @var ProductOption $productOption */
$productOption = $objectManager->create(ProductOptionFactory::class)->create();
/** @var  ProductOptionExtensionInterface $extensionAttributes */
$extensionAttributes = $objectManager->create(ProductOptionExtensionFactory::class)->create();
$customOptionFactory = $objectManager->create(CustomOptionFactory::class);
$options = [];
/** @var ProductCustomOptionInterface $option */
foreach ($product->getOptions() as $option) {
    /** @var CustomOptionInterface $customOption */
    $customOption = $customOptionFactory->create();
    $customOption->setOptionId($option->getId());
    $customOption->setOptionValue($optionValue[$option->getType()]);
    $options[] = $customOption;
}

$quote->load('test_order_1', 'reserved_order_id');
$cartItem->setQty(1);
$cartItem->setSku('simple');
$cartItem->setQuoteId($quote->getId());

$extensionAttributes->setCustomOptions($options);
$productOption->setExtensionAttributes($extensionAttributes);
$cartItem->setProductOption($productOption);

$quoteItemRepository->save($cartItem);
