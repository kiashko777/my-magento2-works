<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\Wishlist\Model\Wishlist;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_special_price.php');

/** @var ObjectManagerInterface $objectManager */
$objectManager = Bootstrap::getObjectManager();
/** @var CustomerRegistry $customerRegistry */
$customerRegistry = Bootstrap::getObjectManager()->create(CustomerRegistry::class);
$customer = $customerRegistry->retrieve(1);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

$product = $productRepository->get('simple');
$product->setStockData(['enable_qty_increments' => 1, 'qty_increments' => 5])->save();

/** @var Wishlist $wishlist */
$wishlist = $objectManager->create(Wishlist::class);
$wishlist->loadByCustomerId($customer->getId(), true);
$wishlist->addNewItem($product);
$wishlist->save();
