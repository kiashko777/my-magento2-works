<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Model\Entity;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple_with_full_option_set.php');

/** @var ObjectManager $objectManager */
$objectManager = Bootstrap::getObjectManager();

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

/** @var $installer CategorySetup */
$installer = $objectManager->create(CategorySetup::class);
$entityModel = $objectManager->create(Entity::class);
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$entityTypeId = $entityModel->setType(Product::ENTITY)->getTypeId();
$groupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

/** @var Product $product */
$product = $productRepository->get('simple', true);

/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$attribute = $objectManager->create(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);
$attribute->setAttributeCode(
    'attribute_code_custom'
)->setEntityTypeId(
    $entityTypeId
)->setIsVisible(true)->setFrontendInput(
    'text'
)->setFrontendLabel(
    'custom_attributes_frontend_label'
)->setAttributeSetId(
    $product->getDefaultAttributeSetId()
)->setAttributeGroupId(
    $groupId
)->setIsFilterable(
    1
)->setIsUserDefined(
    1
)->setBackendType(
    $attribute->getBackendTypeByInput($attribute->getFrontendInput())
)->save();

$product->setCustomAttribute($attribute->getAttributeCode(), 'customAttributeValue');

$productRepository->save($product);
