<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Category;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;

/** @var Category $category */
$category = Bootstrap::getObjectManager()->create(Category::class);
$category->isObjectNew(true);
$category->setId(
    555
)->setCreatedAt(
    '2017-05-5 09:50:07'
)->setName(
    'category-admin'
)->setParentId(
    2
)->setPath(
    '1/2/555'
)->setLevel(
    2
)->setAvailableSortBy(
    ['position', 'name']
)->setDefaultSortBy(
    'name'
)->setIsActive(
    true
)->setPosition(
    1
)->setUrlKey(
    'category-admin'
)->save();

/** @var Store $store */
$store = Bootstrap::getObjectManager()->create(Store::class);

$category->setStoreId($store->load('default')->getId())
    ->setName('category-defaultstore')
    ->setUrlKey('category-defaultstore')
    ->save();

$category->setStoreId($store->load('fixturestore')->getId())
    ->setName('category-fixturestore')
    ->setUrlKey('category-fixturestore')
    ->save();
