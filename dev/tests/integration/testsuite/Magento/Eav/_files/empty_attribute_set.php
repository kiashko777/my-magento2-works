<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Set $attributeSet */
$attributeSet = $objectManager->create(Set::class);
$entityTypeId = $objectManager->create(\Magento\Eav\Model\Entity\Type::class)->loadByCode('catalog_product')->getId();
$attributeSet->setData([
    'attribute_set_name' => 'empty_attribute_set',
    'entity_type_id' => $entityTypeId,
    'sort_order' => 200,
]);
$attributeSet->validate();
$attributeSet->save();
