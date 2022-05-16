<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $objectManager ObjectManager */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;

$objectManager = Bootstrap::getObjectManager();
$product = $objectManager->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(99)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products')
    ->setSku('simple-99')
    ->setPrice(10)
    ->setWeight(1)
    ->setShortDescription("Short description")
    ->setTaxClassId(0)
    ->setDescription('Description with <b>html tag</b>')
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(
        [
            'use_config_manage_stock' => 1,
            'qty' => 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        ]
    )
    ->setCanSaveCustomOptions(true)
    ->setHasOptions(true);

/** @var ProductRepositoryInterface $productRepositoryFactory */
$productRepositoryFactory = $objectManager->get(ProductRepositoryInterface::class);
$productRepositoryFactory->save($product);
