<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $category Category */

use Magento\Catalog\Model\Category;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

// products from this fixture were moved to indexer_catalog_products.php

$categoryFirst = $objectManager->create(Category::class);
$categoryFirst->setName('Category 1')
    ->setPath('1/2')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

$categorySecond = $objectManager->create(Category::class);
$categorySecond->setName('Category 2')
    ->setPath('1/2')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(2)
    ->save();

$categoryThird = $objectManager->create(Category::class);
$categoryThird->setName('Category 3')
    ->setPath($categoryFirst->getPath())
    ->setLevel(3)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(2)
    ->save();

$categoryFourth = $objectManager->create(Category::class);
$categoryFourth->setName('Category 4')
    ->setPath($categoryThird->getPath())
    ->setLevel(4)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

$categoryFifth = $objectManager->create(Category::class);
$categoryFifth->setName('Category 5')
    ->setPath($categorySecond->getPath())
    ->setLevel(3)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(2)
    ->save();
