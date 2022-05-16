<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\Wishlist\Model\Wishlist;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/two_customers.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple.php');

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple');
$firstCustomerIdFromFixture = 1;
$wishlistForFirstCustomer = $objectManager->create(
    Wishlist::class
);
$wishlistForFirstCustomer->loadByCustomerId($firstCustomerIdFromFixture, true);
$item = $wishlistForFirstCustomer->addNewItem($product, new DataObject([]));
$wishlistForFirstCustomer->save();

$secondCustomerIdFromFixture = 2;
$wishlistForSecondCustomer = $objectManager->create(
    Wishlist::class
);
$wishlistForSecondCustomer->loadByCustomerId($secondCustomerIdFromFixture, true);
$item = $wishlistForSecondCustomer->addNewItem($product, new DataObject([]));
$wishlistForSecondCustomer->save();
