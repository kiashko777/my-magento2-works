<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\_files\MultiselectSourceMock;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/multiselect_attribute_with_source_model.php');

/** Create product with options and multiselect attribute */

/** @var $installer CategorySetup */
$installer = Bootstrap::getObjectManager()->create(
    CategorySetup::class
);

/** @var $sourceModel MultiselectSourceMock */
$sourceModel = Bootstrap::getObjectManager()->create(
    MultiselectSourceMock::class
);
$options = $sourceModel->getAllOptions();

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId($options[0]['value'] * 10)
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
    ->setWebsiteIds([1])
    ->setName('With Multiselect Source Model 1')
    ->setSku('simple_mssm_1')
    ->setPrice(10)
    ->setDescription('Hello " &amp;" Bring the water bottle when you can!')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setMultiselectAttrWithSource([$options[0]['value']])
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->save();

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId($options[1]['value'] * 10)
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
    ->setWebsiteIds([1])
    ->setName('With Multiselect Source Model 2')
    ->setSku('simple_mssm_2')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setMultiselectAttrWithSource([$options[1]['value'], $options[2]['value'], $options[3]['value']])
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->save();
