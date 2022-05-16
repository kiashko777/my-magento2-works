<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $category Category */

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var $productFirst Product */
$productFirst = $objectManager->create(Product::class);
$productFirst->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products Apple')
    ->setSku('fulltext-1')
    ->setUrlKey('fulltext-1')
    ->setPrice(10)
    ->setMetaTitle('first meta title')
    ->setMetaKeyword('first meta keyword')
    ->setMetaDescription('first meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

/** @var $productFirst Product */
$productSecond = $objectManager->create(Product::class);
$productSecond->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products Banana')
    ->setSku('fulltext-2')
    ->setUrlKey('fulltext-2')
    ->setPrice(20)
    ->setMetaTitle('second meta title')
    ->setMetaKeyword('second meta keyword')
    ->setMetaDescription('second meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

/** @var $productFirst Product */
$productThird = $objectManager->create(Product::class);
$productThird->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products Orange')
    ->setSku('fulltext-3')
    ->setUrlKey('fulltext-3')
    ->setPrice(20)
    ->setMetaTitle('third meta title')
    ->setMetaKeyword('third meta keyword')
    ->setMetaDescription('third meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

/** @var $productFirst Product */
$productFourth = $objectManager->create(Product::class);
$productFourth->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products Papaya')
    ->setSku('fulltext-4')
    ->setUrlKey('fulltext-4')
    ->setPrice(20)
    ->setMetaTitle('fourth meta title')
    ->setMetaKeyword('fourth meta keyword')
    ->setMetaDescription('fourth meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();

/** @var $productFirst Product */
$productFifth = $objectManager->create(Product::class);
$productFifth->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products Cherry')
    ->setSku('fulltext-5')
    ->setUrlKey('fulltext-5')
    ->setPrice(20)
    ->setMetaTitle('fifth meta title')
    ->setMetaKeyword('fifth meta keyword')
    ->setMetaDescription('fifth meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0])
    ->save();
