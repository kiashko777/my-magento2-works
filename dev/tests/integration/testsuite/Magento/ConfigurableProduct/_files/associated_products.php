<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var ProductRepositoryInterface $productRepository */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$productRepository = Bootstrap::getObjectManager()
    ->create(ProductRepositoryInterface::class);

/** @var $product Product */
$product = Bootstrap::getObjectManager()
    ->create(Product::class);
$product
    ->setTypeId('simple')
    ->setId(3)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products 3')
    ->setSku('simple_3')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setQty(22);
$product = $productRepository->save($product);

$product = Bootstrap::getObjectManager()
    ->create(Product::class);
$product
    ->setTypeId('simple')
    ->setId(14)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products 14')
    ->setSku('simple_14')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setQty(22);
$product = $productRepository->save($product);

$product = Bootstrap::getObjectManager()
    ->create(Product::class);
$product
    ->setTypeId('simple')
    ->setId(15)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products 15')
    ->setSku('simple_15')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setQty(22);
$product = $productRepository->save($product);

$product = Bootstrap::getObjectManager()
    ->create(Product::class);
$product
    ->setTypeId('simple')
    ->setId(92)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products 92')
    ->setSku('simple_92')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setQty(22);
$product = $productRepository->save($product);
