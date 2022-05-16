<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_date_attribute.php');

$objectManager = Bootstrap::getObjectManager();
/** @var Config $eavConfig */
$eavConfig = $objectManager->get(Config::class);
$attribute = $eavConfig->getAttribute(Product::ENTITY, 'date_attribute');
$attribute->setData('is_used_for_promo_rules', 1);

/** @var ProductFactory $productFactory */
$productFactory = $objectManager->get(Magento\Catalog\Model\ProductFactory::class);
$product = $productFactory->create();
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId($product->getDefaultAttributeSetId())
    ->setWebsiteIds([1])
    ->setName('Simple Products with date')
    ->setSku('simple_with_date')
    ->setPrice(10)
    ->setDescription('Description with <b>html tag</b>')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setCategoryIds([2])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setDateAttribute(date('Y-m-d'))
    ->setUrlKey('simple_with_date')
    ->save();

$product2 = $productFactory->create();
$product2->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId($product->getDefaultAttributeSetId())
    ->setWebsiteIds([1])
    ->setName('Simple Products with date -1')
    ->setSku('simple_with_date2')
    ->setPrice(10)
    ->setDescription('Description with <b>html tag</b>')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setCategoryIds([2])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setDateAttribute(date('Y-m-d', strtotime(date('Y-m-d') . '-1 day')))
    ->setUrlKey('simple_with_date2')
    ->save();

$product3 = $productFactory->create();
$product3->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId($product->getDefaultAttributeSetId())
    ->setWebsiteIds([1])
    ->setName('Simple Products with date +1')
    ->setSku('simple_with_date3')
    ->setPrice(10)
    ->setDescription('Description with <b>html tag</b>')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setCategoryIds([2])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setDateAttribute(date('Y-m-d', strtotime(date('Y-m-d') . '+1 day')))
    ->setUrlKey('simple_with_date3')
    ->save();
