<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Registry $registry */

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogInventory\Model\StockRegistryStorage;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var Collection $collection */
$collection = $objectManager->create(Collection::class);
$collection->addAttributeToSelect('id')->load();
if ($collection->count() > 0) {
    $collection->delete();
}

/** @var StockRegistryStorage $stockRegistryStorage */
$stockRegistryStorage = $objectManager->get(StockRegistryStorage::class);
$stockRegistryStorage->clean();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
