<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Store/_files/core_fixturestore.php');
$objectManager = Bootstrap::getObjectManager();

/** @var Magento\Store\Model\Store $store */
$store = $objectManager->create(Store::class);
$store->load('fixturestore', 'code');

/** @var $product Product */
$product = $objectManager->create(Product::class);

$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    1
)->setAttributeSetId(
    4
)->setCustomAttribute(
    'tax_class_id',
    1
)->setCustomAttribute(
    'tax_class_id',
    1
)->setWebsiteIds(
    [1]
)->setName(
    'Simple Products One'
)->setSku(
    'simple'
)->setPrice(
    10
)->setWeight(
    18
)->setStockData(
    ['use_config_manage_stock' => 0]
)->setCategoryIds(
    [9]
)->setVisibility(
    Visibility::VISIBILITY_BOTH
)->setStatus(
    Status::STATUS_ENABLED
)->save();

$product = $objectManager->create(Product::class);
$product->load(1)
    ->setStoreId($store->getId())
    ->setName('StoreTitle')
    ->save();
