<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_boolean_attribute_rollback.php');

/** @var $objectManager ObjectManagerInterface */
$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var Collection $collection */
$collection = $objectManager->create(Collection::class);
$collection->addAttributeToSelect('id')->load();
if ($collection->count() > 0) {
    $collection->delete();
}

/** @var Store $store */
$store = $objectManager->create(Store::class);
$storeCode = 'secondary';
$store->load($storeCode);
if ($store->getId()) {
    $store->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
