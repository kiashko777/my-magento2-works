<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Bundle/_files/multiple_products.php');

$objectManager = Bootstrap::getObjectManager();
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE)
    ->setId(42)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Bundle Products')
    ->setSku('bundle_product')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setPriceView(0)
    ->setPriceType(Price::PRICE_TYPE_FIXED)
    ->setPrice(110.0)
    ->setShipmentType(0);

$productRepository->save($product);
