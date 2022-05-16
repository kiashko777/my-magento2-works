<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var $category Category */

use Magento\Catalog\Model\Category;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$categoryFirst = $objectManager->create(Category::class);
$categoryFirst->setName('Category 1')
    ->setPath('1/2')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

// products from this fixture were moved to indexer_catalog_products.php
$categorySecond = $objectManager->create(Category::class);
$categorySecond->setName('"Category 6"')
    ->setPath($categoryFirst->getPath())
    ->setLevel(3)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();
