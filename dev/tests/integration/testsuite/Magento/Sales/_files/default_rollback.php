<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\StockRegistryStorage;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Registry $registry */
$registry = Bootstrap::getObjectManager()->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $order Order */
$orderCollection = Bootstrap::getObjectManager()->create(\Magento\Sales\Model\ResourceModel\Order\Collection::class);
foreach ($orderCollection as $order) {
    $order->delete();
}

/** @var $product Product */
$productCollection = Bootstrap::getObjectManager()->create(
    Collection::class
);
foreach ($productCollection as $product) {
    $product->delete();
}

/** @var StockRegistryStorage $stockRegistryStorage */
$stockRegistryStorage = Bootstrap::getObjectManager()
    ->get(StockRegistryStorage::class);
$stockRegistryStorage->clean();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
