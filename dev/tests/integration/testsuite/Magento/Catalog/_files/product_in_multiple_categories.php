<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$category = Bootstrap::getObjectManager()->create(Category::class);
$category->isObjectNew(true);
$category->setId(
    333
)->setCreatedAt(
    '2014-06-23 09:50:07'
)->setName(
    'Category 1'
)->setParentId(
    2
)->setPath(
    '1/2/3'
)->setLevel(
    2
)->setAvailableSortBy(
    'name'
)->setDefaultSortBy(
    'name'
)->setIsActive(
    true
)->setPosition(
    1
)->setAvailableSortBy(
    ['position']
)->save();

$category = Bootstrap::getObjectManager()->create(Category::class);
$category->isObjectNew(true);
$category->setId(4)
    ->setName('Category 2')
    ->setParentId(2)
    ->setPath('1/2/4')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setIsAnchor(true)
    ->setPosition(1)
    ->save();

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    333
)->setAttributeSetId(
    4
)->setStoreId(
    1
)->setWebsiteIds(
    [1]
)->setName(
    'Simple Products Three'
)->setSku(
    'simple333'
)->setPrice(
    10
)->setWeight(
    18
)->setStockData(
    ['use_config_manage_stock' => 0]
)->setCategoryIds(
    [333, 4]
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->save();

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    334
)->setAttributeSetId(
    4
)->setStoreId(
    1
)->setWebsiteIds(
    [1]
)->setName(
    'Simple Products four'
)->setSku(
    'simple444'
)->setPrice(
    10
)->setWeight(
    18
)->setStockData(
    ['use_config_manage_stock' => 0]
)->setCategoryIds(
    [333, 4]
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->save();
