<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/products_upsell_rollback.php');

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    1
)->setAttributeSetId(
    4
)->setName(
    'Simple Up Sell'
)->setSku(
    'simple'
)->setPrice(
    100
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
$productLink = Bootstrap::getObjectManager()->create(
    ProductLinkInterface::class
);
$productLink->setSku('simple_with_upsell');
$productLink->setLinkedProductSku('simple');
$productLink->setPosition(1);
$productLink->setLinkType('upsell');

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    2
)->setAttributeSetId(
    4
)->setName(
    'Simple Products With Up Sell'
)->setSku(
    'simple_with_upsell'
)->setPrice(
    10
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->setWebsiteIds(
    [1]
)->setStockData(
    ['qty' => 100, 'is_in_stock' => 1]
)->setProductLinks(
    [$productLink]
)->save();
