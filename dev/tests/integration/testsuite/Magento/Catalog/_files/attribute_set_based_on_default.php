<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$attributeSet = $objectManager->create(Set::class);
$entityType = $objectManager->create(\Magento\Eav\Model\Entity\Type::class)->loadByCode('catalog_product');
$defaultSetId = $objectManager->create(Product::class)->getDefaultAttributeSetid();
$data = [
    'attribute_set_name' => 'new_attribute_set',
    'entity_type_id' => $entityType->getId(),
    'sort_order' => 300,
];

$attributeSet->setData($data);
$attributeSet->validate();
$attributeSet->save();
$attributeSet->initFromSkeleton($defaultSetId);
$attributeSet->save();
