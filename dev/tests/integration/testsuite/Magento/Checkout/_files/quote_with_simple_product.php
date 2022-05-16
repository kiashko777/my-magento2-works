<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/products.php');

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->create(ProductRepositoryInterface::class);
/** @var $product Product */
$product = $productRepository->get('simple');

$requestInfo = new DataObject(['qty' => 1]);

/** @var $cart Cart */
$cart = Bootstrap::getObjectManager()->create(Cart::class);
$cart->addProduct($product, $requestInfo);
$cart->save();

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();
$objectManager->removeSharedInstance(Session::class);
