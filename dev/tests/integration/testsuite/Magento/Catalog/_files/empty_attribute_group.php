<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Eav\Model\Entity\Attribute\Group;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Set $attributeSet */
$attributeGroup = $objectManager->create(Group::class);
$entityTypeId = $objectManager->create(\Magento\Eav\Model\Entity\Type::class)->loadByCode('catalog_product')->getId();
$attributeGroup->setData([
    'attribute_group_name' => 'empty_attribute_group',
    'attribute_set_id' => 1,
]);
$attributeGroup->save();
