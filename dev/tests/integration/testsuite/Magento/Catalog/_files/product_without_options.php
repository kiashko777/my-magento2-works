<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $product Product */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    'simple'
)->setId(
    1
)->setAttributeSetId(
    4
)->setWebsiteIds(
    [1]
)->setName(
    'Simple Products Without Custom Options'
)->setSku(
    'simple'
)->setPrice(
    10
)->setMetaTitle(
    'meta title'
)->setMetaKeyword(
    'meta keyword'
)->setMetaDescription(
    'meta description'
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->setQty(
    100
)->save();
