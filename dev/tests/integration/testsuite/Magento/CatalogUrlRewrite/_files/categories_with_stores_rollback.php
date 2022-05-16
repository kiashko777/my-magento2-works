<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/second_store_rollback.php');

/** @var Registry $registry */
$objectManager = Bootstrap::getObjectManager();
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
$collection = $objectManager->create(Collection::class);
$collection
    ->addAttributeToFilter('level', 2)
    ->load()
    ->delete();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
