<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var ObjectManagerInterface $objectManager */

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
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
