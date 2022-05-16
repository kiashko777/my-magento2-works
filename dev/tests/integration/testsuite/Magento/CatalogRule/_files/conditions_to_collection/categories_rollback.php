<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

//Remove categories
/** @var Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
$collection = $objectManager->create(Collection::class);
$collection->addAttributeToFilter('level', ['gt' => 1]);

foreach ($collection as $category) {
    /** @var Category $category */
    if ($category->getLevel() !== 1) {
        $category->delete();
    }
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
