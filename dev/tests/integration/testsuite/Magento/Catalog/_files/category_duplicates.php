<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$category = Bootstrap::getObjectManager()->create(Category::class);
$category->isObjectNew(true);
$category->setId(
    '444'
)->setName(
    'Category 2'
)->setAttributeSetId(
    '3'
)->setParentId(
    2
)->setPath(
    '1/2/444'
)->setLevel(
    '2'
)->setDefaultSortBy(
    'name'
)->setIsActive(
    true
)->save();

$productModel = Bootstrap::getObjectManager()->create(
    Product::class
);

$productModel->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    1
)->setAttributeSetId(
    4
)->setName(
    'New Products'
)->setSku(
    'simple3'
)->setPrice(
    10
)->setTierPrice(
    [0 => ['website_id' => 0, 'cust_group' => 0, 'price_qty' => 3, 'price' => 8]]
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->setWebsiteIds(
    [1]
)->setCategoryIds(
    []
)->setStockData(
    ['qty' => 100, 'is_in_stock' => 1, 'manage_stock' => 1]
)->setCategoryIds(
    [444]
)->save();
