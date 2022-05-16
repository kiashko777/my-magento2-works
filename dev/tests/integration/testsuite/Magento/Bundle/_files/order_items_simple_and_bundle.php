<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Bundle/_files/order_item_with_bundle_and_options.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/category_product.php');

$objectManager = Bootstrap::getObjectManager();
$order = $objectManager->get(OrderInterfaceFactory::class)->create()->loadByIncrementId('100000001');
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple333');
/** @var Item $orderItem */
$orderItem = $objectManager->create(Item::class);
/** @var $product Product */
$orderItem->setProductId($product->getId())->setQtyOrdered(1);
$orderItem->setBasePrice($product->getPrice());
$orderItem->setPrice($product->getPrice());
$orderItem->setRowTotal($product->getPrice());
$orderItem->setProductType('simple');

/** @var Order $order */
$order->addItem($orderItem);
$order->save();
