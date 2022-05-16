<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * After installation system has two categories: root one with ID:1 and Default category with ID:2
 */
/** @var $category Category */

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$category = Bootstrap::getObjectManager()->create(Category::class);
$category->setName(
    'Category Without Image 1'
)->setParentId(
    2
)->setLevel(
    2
)->setAvailableSortBy(
    'name'
)->setDefaultSortBy(
    'name'
)->setIsActive(
    true
)->save();

/** @var $objectManager ObjectManager */
$objectManager = Bootstrap::getObjectManager();
$objectManager->get(Registry::class)->register('_fixture/Magento\Catalog\Model\Category', $category);
