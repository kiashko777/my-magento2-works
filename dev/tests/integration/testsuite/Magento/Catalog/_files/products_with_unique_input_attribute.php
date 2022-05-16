<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Eav\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/unique_input_attribute.php');

$objectManager = Bootstrap::getObjectManager();
$eavConfig = $objectManager->get(Config::class);
$attribute = $eavConfig->getAttribute(Product::ENTITY, 'unique_input_attribute');

/** @var $product Product */
$product = $objectManager->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId(4)
    ->setName('Simple Products with unique input attribute')
    ->setSku('simple product with unique input attribute')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['qty' => 100, 'is_in_stock' => 1, 'manage_stock' => 1])
    ->setData($attribute->getAttributeCode(), 'unique value')
    ->save();
