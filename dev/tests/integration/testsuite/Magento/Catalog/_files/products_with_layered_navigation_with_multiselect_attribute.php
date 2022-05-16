<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\CacheCleaner;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

/**
 * Create multiselect attribute
 */
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/multiselect_attribute.php');

/** Create product with options and multiselect attribute */

/** @var $installer CategorySetup */
$installer = Bootstrap::getObjectManager()->create(
    CategorySetup::class
);

/** @var $options Collection */
$options = Bootstrap::getObjectManager()->create(
    Collection::class
);
$eavConfig = Bootstrap::getObjectManager()->get(Config::class);

/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$attribute = $eavConfig->getAttribute('catalog_product', 'multiselect_attribute');

$eavConfig->clear();
$attribute->setIsSearchable(1)
    ->setIsVisibleInAdvancedSearch(1)
    ->setIsFilterable(true)
    ->setIsFilterableInSearch(true)
    ->setIsVisibleOnFront(1);
/** @var AttributeRepositoryInterface $attributeRepository */
$attributeRepository = Bootstrap::getObjectManager()->create(AttributeRepositoryInterface::class);
$attributeRepository->save($attribute);

$options->setAttributeFilter($attribute->getId());
$optionIds = $options->getAllIds();

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId($optionIds[0] * 10)
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
    ->setWebsiteIds([1])
    ->setName('With Multiselect 1 and 2')
    ->setSku('simple_ms_1')
    ->setPrice(10)
    ->setDescription('Hello " &amp;" Bring the water bottle when you can!')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setMultiselectAttribute([$optionIds[1], $optionIds[2]])
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1]);
$productRepository->save($product);

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId($optionIds[1] * 10)
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
    ->setWebsiteIds([1])
    ->setName('With Multiselect 2 and 3')
    ->setSku('simple_ms_2')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setMultiselectAttribute([$optionIds[2], $optionIds[3]])
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1]);
$productRepository->save($product);

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId($optionIds[2] * 10)
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
    ->setWebsiteIds([1])
    ->setName('With Multiselect 1 and 3')
    ->setSku('simple_ms_2')
    ->setPrice(10)
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setMultiselectAttribute([$optionIds[2], $optionIds[3]])
    ->setStatus(Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1]);

$productRepository->save($product);

CacheCleaner::cleanAll();
