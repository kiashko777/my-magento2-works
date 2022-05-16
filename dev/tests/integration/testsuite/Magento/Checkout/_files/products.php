<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/default_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/Checkout/_files/rollback_quote.php');

/** @var $product Product */
$product1 = Bootstrap::getObjectManager()->create(Product::class);
$product1->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setAttributeSetId(
    4
)->setWebsiteIds(
    [1]
)->setName(
    'Simple Products 1'
)->setSku(
    'Simple Products 1 sku'
)->setPrice(
    10
)->setDescription(
    'Description with <b>html tag</b>'
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
)->setCategoryIds(
    [2]
)->setStockData(
    ['use_config_manage_stock' => 0]
)->setCanSaveCustomOptions(
    true
)->setHasOptions(
    true
);

$product2 = clone $product1;
$product2->setName('Simple Products 2')->setSku('Simple Products 2 sku')->save();
$product3 = clone $product1;
$product3->setName('Simple Products 3')->setSku('Simple Products 3 sku')->save();
$product4 = clone $product1;
$product4->setName('Simple Products 4')->setSku('Simple Products 4 sku')->save();
$product1->save();
