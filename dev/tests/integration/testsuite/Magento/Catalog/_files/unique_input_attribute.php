<?php
/**
 * "Input" fixture of product EAV attribute.
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var \Magento\Eav\Model\Entity\Type $entityType */

use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\TestFramework\Helper\Bootstrap;

$entityType = Bootstrap::getObjectManager()->create(
    \Magento\Eav\Model\Entity\Type::class
);
$entityType->loadByCode('catalog_product');
$defaultSetId = $entityType->getDefaultAttributeSetId();
/** @var Set $defaultSet */
$defaultSet = Bootstrap::getObjectManager()->create(
    Set::class
);
$defaultSet->load($defaultSetId);
$defaultGroupId = $defaultSet->getDefaultGroupId();

/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$attribute = Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$attribute->setAttributeCode(
    'unique_input_attribute'
)->setEntityTypeId(
    $entityType->getEntityTypeId()
)->setAttributeGroupId(
    $defaultGroupId
)->setAttributeSetId(
    $defaultSetId
)->setFrontendInput(
    'text'
)->setFrontendLabel(
    'Unique Input Attribute'
)->setBackendType(
    'varchar'
)->setIsUserDefined(
    1
)->setIsUnique(
    1
)->save();
