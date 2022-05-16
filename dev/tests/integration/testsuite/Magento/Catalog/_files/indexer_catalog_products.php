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
use Magento\Catalog\Model\ProductRepository;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$productRepository = $objectManager->get(
    ProductRepository::class
);

/** @var $productFirst Product */
$productFirst = $objectManager->create(Product::class);
$productFirst->setTypeId('simple')
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products 01')
    ->setSku('simple 01')
    ->setPrice(10)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0]);
$productRepository->save($productFirst);

/** @var $productSecond Product */
$productSecond = $objectManager->create(Product::class);
$productSecond->setTypeId('simple')
    ->setId(2)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products 02')
    ->setSku('simple 02')
    ->setPrice(10)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0]);
$productRepository->save($productSecond);

/** @var $productThird Product */
$productThird = $objectManager->create(Product::class);
$productThird->setTypeId('simple')
    ->setId(3)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products 03')
    ->setSku('simple 03')
    ->setPrice(10)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 0]);
$productRepository->save($productThird);
