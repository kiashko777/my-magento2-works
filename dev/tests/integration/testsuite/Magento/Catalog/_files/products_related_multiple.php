<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $product Product */

use Magento\Catalog\Api\Data\ProductLinkInterface;
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
    3
)->setAttributeSetId(
    4
)->setName(
    'Simple Products With Related Products Two'
)->setSku(
    'simple_with_cross_two'
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

/** @var ProductLinkInterface $productLink */
$productLink1 = Bootstrap::getObjectManager()
    ->create(ProductLinkInterface::class);
$productLink1->setSku('simple_with_cross');
$productLink1->setLinkedProductSku('simple');
$productLink1->setPosition(1);
$productLink1->setLinkType('related');

/** @var ProductLinkInterface $productLink */
$productLink2 = Bootstrap::getObjectManager()
    ->create(ProductLinkInterface::class);
$productLink2->setSku('simple_with_cross');
$productLink2->setLinkedProductSku('simple_with_cross_two');
$productLink2->setPosition(1);
$productLink2->setLinkType('related');

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    2
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
)->setProductLinks(
    [$productLink1, $productLink2]
)->save();
