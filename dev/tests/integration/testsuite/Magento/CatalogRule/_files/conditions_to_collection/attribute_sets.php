<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\AttributeSetRepositoryInterface;
use Magento\Eav\Api\AttributeSetManagementInterface;
use Magento\Eav\Api\Data\AttributeSetInterface;
use Magento\Eav\Api\Data\AttributeSetInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$attributeSetFactory = $objectManager->get(AttributeSetInterfaceFactory::class);
$dataObjectHelper = $objectManager->get(DataObjectHelper::class);
$attributeSetRepository = $objectManager->get(AttributeSetRepositoryInterface::class);
$attributeSetManagement = $objectManager->get(AttributeSetManagementInterface::class);

$entityTypeId = $objectManager->create(\Magento\Eav\Model\Entity\Type::class)->loadByCode('catalog_product')->getId();
$defaultAttributeSet = $objectManager->get(Magento\Eav\Model\Config::class)
    ->getEntityType('catalog_product')
    ->getDefaultAttributeSetId();

$attributeSet = $attributeSetFactory->create();
$dataObjectHelper->populateWithArray(
    $attributeSet,
    [
        'attribute_set_name' => 'Super Powerful Muffins',
        'entity_type_id' => $entityTypeId,
    ],
    AttributeSetInterface::class
);
$attributeSetManagement->create('catalog_product', $attributeSet, $defaultAttributeSet)->save();


$attributeSet = $attributeSetFactory->create();
$dataObjectHelper->populateWithArray(
    $attributeSet,
    [
        'attribute_set_name' => 'Banana Rangers',
        'entity_type_id' => $entityTypeId,
    ],
    AttributeSetInterface::class
);
$attributeSetManagement->create('catalog_product', $attributeSet, $defaultAttributeSet)->save();

$attributeSet = $attributeSetFactory->create();
$dataObjectHelper->populateWithArray(
    $attributeSet,
    [
        'attribute_set_name' => 'Guardians of the Refrigerator',
        'entity_type_id' => $entityTypeId,
    ],
    AttributeSetInterface::class
);
$attributeSetManagement->create('catalog_product', $attributeSet, $defaultAttributeSet)->save();
