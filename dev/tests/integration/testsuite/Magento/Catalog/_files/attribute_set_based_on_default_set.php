<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $product Product */

use Magento\Catalog\Model\Product;
use Magento\Eav\Api\Data\AttributeSetInterface;
use Magento\Eav\Model\AttributeSetManagement;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var AttributeSetManagement $attributeSetManagement */
$attributeSetManagement = $objectManager->create(AttributeSetManagement::class);

/** @var AttributeSetInterface $attributeSet */
$attributeSet = $objectManager->create(Set::class);

$data = [
    'attribute_set_name' => 'second_attribute_set',
    'sort_order' => 200,
];

$attributeSet->organizeData($data);

$defaultSetId = $objectManager->create(Product::class)->getDefaultAttributeSetId();

$attributeSetManagement->create(Product::ENTITY, $attributeSet, $defaultSetId);
