<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Option;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple.php');

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple');

$options = [];
/** @var $option Option */
foreach ($product->getOptions() as $option) {
    switch ($option->getGroupByType()) {
        case ProductCustomOptionInterface::OPTION_GROUP_DATE:
            $value = ['year' => 2013, 'month' => 8, 'day' => 9, 'hour' => 13, 'minute' => 35];
            break;
        case ProductCustomOptionInterface::OPTION_GROUP_SELECT:
            $value = key($option->getValues());
            break;
        default:
            $value = 'test';
            break;
    }
    $options[$option->getId()] = $value;
}

$requestInfo = new DataObject(['qty' => 1, 'options' => $options]);

/** @var $cart Cart */
$cart = Bootstrap::getObjectManager()->create(Cart::class);
$cart->addProduct($product, $requestInfo);
$cart->save();

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();
$objectManager->removeSharedInstance(Session::class);
