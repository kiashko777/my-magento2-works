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

$objectManager = Bootstrap::getObjectManager();
Bootstrap::getInstance()->reinitialize();
/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId(4)
    ->setName('Simple Cross Sell')
    ->setSku('simple')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['qty' => 100, 'is_in_stock' => 1, 'manage_stock' => 1])
    ->save();

/** @var ProductLinkInterface $productLink */
$productLink = $objectManager->create(ProductLinkInterface::class);
$productLink->setSku('simple_with_cross');
$productLink->setLinkedProductSku('simple');
$productLink->setPosition(1);
$productLink->setLinkType('crosssell');

$product = $objectManager->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId(4)
    ->setName('Simple Products With Cross Sell')
    ->setSku('simple_with_cross')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['qty' => 100, 'is_in_stock' => 1, 'manage_stock' => 1])
    ->setProductLinks([$productLink])
    ->save();
