<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Category;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/categories.php');

$objectManager = Bootstrap::getObjectManager();

// Adding 4th level ensures an edge case for which 3 levels of categories would not be enough
$category = $objectManager->create(Category::class);
$category->isObjectNew(true);
$category->setId(59)
    ->setName('Category 1.1.1.1')
    ->setParentId(5)
    ->setPath('1/2/3/4/5/59')
    ->setLevel(5)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->setCustomUseParentSettings(0)
    ->setCustomDesign('Magento/blank')
    ->setDescription('This is the description for Category 1.1.1.1')
    ->save();

/** @var $category Category */
$category = $objectManager->create(Category::class);

// Category 1.1.1
$category->load(4);
$category->setIsActive(false);
$category->save();
