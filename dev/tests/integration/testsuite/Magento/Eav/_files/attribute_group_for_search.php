<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Eav\Model\Entity\Attribute\Group;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/**
 * Create attribute set
 */
$entityTypeId = $objectManager->create(\Magento\Eav\Model\Entity\Type::class)
    ->loadByCode('catalog_product')
    ->getId();

/** @var Set $attributeSet */
$attributeSet = $objectManager->create(Set::class);
$attributeSet->setData([
    'attribute_set_name' => 'attribute_set_1_for_search',
    'entity_type_id' => $entityTypeId,
    'sort_order' => 100,
]);
$attributeSet->validate();
$attributeSet->save();

/**
 * Create attribute groups
 */
$attributeGroupData = [
    [
        'attribute_set_id' => $attributeSet->getAttributeSetId(),
        'sort_order' => 10,
        'attribute_group_code' => 'attribute_group_1_for_search',
        'default_id' => 1,
    ],
    [
        'attribute_set_id' => $attributeSet->getAttributeSetId(),
        'sort_order' => 20,
        'attribute_group_code' => 'attribute_group_2_for_search',
        'default_id' => 0,
    ],
    [
        'attribute_set_id' => $attributeSet->getAttributeSetId(),
        'sort_order' => 30,
        'attribute_group_code' => 'attribute_group_3_for_search',
        'default_id' => 0,
    ],
];

foreach ($attributeGroupData as $data) {
    /** @var Group $attributeGroup */
    $attributeGroup = $objectManager->create(Group::class);
    $attributeGroup->setData($data);
    $attributeGroup->save();
}
