<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Eav\Model\Entity\Attribute\Group;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Group $attributeSet */
$attributeGroup = $objectManager->create(Group::class)
    ->load('empty_attribute_group', 'attribute_group_name');
if ($attributeGroup->getId()) {
    $attributeGroup->delete();
}

$attributeGroupUpdated = $objectManager->create(Group::class)
    ->load('empty_attribute_group_updated', 'attribute_group_name');
if ($attributeGroupUpdated->getId()) {
    $attributeGroupUpdated->delete();
}
