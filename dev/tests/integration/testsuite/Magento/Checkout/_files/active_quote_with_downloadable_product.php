<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Downloadable\Model\Link;
use Magento\Downloadable\Model\ResourceModel\Link\Collection;
use Magento\Framework\DataObject;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Downloadable/_files/product_downloadable.php');
Resolver::getInstance()->requireDataFixture('Magento/Checkout/_files/active_quote.php');
/** @var QuoteFactory $quoteFactory */
$quoteFactory = Bootstrap::getObjectManager()->get(QuoteFactory::class);
/** @var QuoteResource $quoteResource */
$quoteResource = Bootstrap::getObjectManager()->get(QuoteResource::class);
$quote = $quoteFactory->create();
$quoteResource->load($quote, 'test_order_1', 'reserved_order_id');

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->create(ProductRepositoryInterface::class);
/** @var $product Product */
$product = $productRepository->get('downloadable-product');

/** @var $linkCollection Collection */
$linkCollection = Bootstrap::getObjectManager()->create(
    Link::class
)->getCollection()->addProductToFilter(
    $product->getId()
)->addTitleToResult(
    $product->getStoreId()
)->addPriceToResult(
    $product->getStore()->getWebsiteId()
);

/** @var $link Link */
$link = $linkCollection->getFirstItem();

$requestInfo = new DataObject(['qty' => 1, 'links' => [$link->getId()]]);

/** @var $cart Cart */
$cart = Bootstrap::getObjectManager()->create(Cart::class);
$cart->setQuote($quote);
$cart->addProduct($product, $requestInfo);
$cart->save();

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();
$objectManager->removeSharedInstance(Session::class);
