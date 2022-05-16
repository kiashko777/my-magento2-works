<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Product;
use Magento\Customer\Model\CustomerRegistry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\Wishlist\Model\WishlistFactory;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/multiple_products.php');

$objectManager = Bootstrap::getObjectManager();
/** @var CustomerRegistry $customerRegistry */
$customerRegistry = Bootstrap::getObjectManager()->create(CustomerRegistry::class);
$customer = $customerRegistry->retrieve(1);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple1');
$product2 = $productRepository->get('simple2');

$wishlistFactory = $objectManager->get(WishlistFactory::class);
$wishlist = $wishlistFactory->create();
$wishlist->loadByCustomerId($customer->getId(), true);
/** @var Product $productHelper */
$productHelper = $objectManager->get(Product::class);
$isSkipSaleableCheck = $productHelper->getSkipSaleableCheck();
$productHelper->setSkipSaleableCheck(true);
$wishlist->addNewItem($product);
$wishlist->addNewItem($product2);
$productHelper->setSkipSaleableCheck($isSkipSaleableCheck);
