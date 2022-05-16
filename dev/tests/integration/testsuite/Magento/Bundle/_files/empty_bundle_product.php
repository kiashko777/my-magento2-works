<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var $objectManager ObjectManager */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->setTypeId('bundle')
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Bundle Products')
    ->setSku('bundle-product')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setPriceView(0)
    ->setPriceType(0)
    ->setShipmentType(1)
    ->setWeightType(0)
    ->setDescription('description')
    ->setPrice(99);

$productRepository->save($product);
