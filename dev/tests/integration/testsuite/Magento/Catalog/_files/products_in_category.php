<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $product Product */

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    333
)->setAttributeSetId(
    4
)->setName(
    'Simple Related Products'
)->setSku(
    'simple'
)->setPrice(
    10
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->setWebsiteIds(
    [1]
)->setStockData(
    ['qty' => 100, 'is_in_stock' => 1, 'manage_stock' => 1]
)->save();

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    334
)->setAttributeSetId(
    4
)->setName(
    'Simple Products With Related Products'
)->setSku(
    'simple_with_cross'
)->setPrice(
    10
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->setWebsiteIds(
    [1]
)->setStockData(
    ['qty' => 100, 'is_in_stock' => 1, 'manage_stock' => 1]
)->save();

/** @var Category $category */
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
)->setPostedProducts(
    [333 => 10]
)->save();
