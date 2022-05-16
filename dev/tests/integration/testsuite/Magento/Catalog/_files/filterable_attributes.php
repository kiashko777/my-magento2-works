<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $installer CategorySetup */

use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Model\Entity;
use Magento\TestFramework\Helper\Bootstrap;

$installer = Bootstrap::getObjectManager()->create(
    CategorySetup::class
);
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$entityModel = Bootstrap::getObjectManager()->create(Entity::class);
$entityTypeId = $entityModel->setType(Product::ENTITY)->getTypeId();
$groupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$attribute = Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$attribute->setAttributeCode(
    'filterable_attribute_a'
)->setEntityTypeId(
    $entityTypeId
)->setAttributeGroupId(
    $groupId
)->setAttributeSetId(
    $attributeSetId
)->setIsFilterable(
    1
)->setIsUserDefined(
    1
)->save();

$attribute = Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$attribute->setAttributeCode(
    'filterable_attribute_b'
)->setEntityTypeId(
    $entityTypeId
)->setAttributeGroupId(
    $groupId
)->setAttributeSetId(
    $attributeSetId
)->setIsFilterable(
    1
)->setIsUserDefined(
    1
)->save();
