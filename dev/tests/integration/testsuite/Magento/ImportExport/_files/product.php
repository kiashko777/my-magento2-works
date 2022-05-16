<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    1
)->setAttributeSetId(
    4
)->setName(
    'New Products'
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
)->setCategoryIds(
    []
)->setStockData(
    ['qty' => 100, 'is_in_stock' => 1, 'manage_stock' => 1]
)->save();
