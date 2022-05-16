<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $product Product */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(10)
    ->setAttributeSetId(4)
    ->setName('Simple Product1')
    ->setSku('simple1')
    ->setTaxClassId('none')
    ->setDescription('description uniqueword')
    ->setShortDescription('short description')
    ->setOptionsContainer('container1')
    ->setMsrpDisplayActualPriceType(\Magento\Msrp\Model\Product\Attribute\Source\Type::TYPE_IN_CART)
    ->setPrice(10)
    ->setWeight(1)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setCategoryIds([])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setSpecialPrice('5.99')
    ->save();

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(11)
    ->setAttributeSetId(4)
    ->setName('Simple Product2')
    ->setSku('simple2')
    ->setTaxClassId('none')
    ->setDescription('description')
    ->setShortDescription('short description')
    ->setOptionsContainer('container1')
    ->setMsrpDisplayActualPriceType(\Magento\Msrp\Model\Product\Attribute\Source\Type::TYPE_ON_GESTURE)
    ->setPrice(20)
    ->setWeight(1)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(Visibility::VISIBILITY_IN_CATALOG)
    ->setStatus(Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setCategoryIds([])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 50, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setSpecialPrice('15.99')
    ->save();

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(12)
    ->setAttributeSetId(4)
    ->setName('Simple Product3')
    ->setSku('simple3')
    ->setTaxClassId('none')
    ->setDescription('description')
    ->setShortDescription('short description')
    ->setPrice(30)
    ->setWeight(1)
    ->setVisibility(Visibility::VISIBILITY_IN_CATALOG)
    ->setStatus(Status::STATUS_DISABLED)
    ->setWebsiteIds([1])
    ->setCategoryIds([])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 140, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setSpecialPrice('25.99')
    ->save();
