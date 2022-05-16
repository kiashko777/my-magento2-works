<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Weee/_files/fixed_product_attribute_rollback.php');

$objectManager = Bootstrap::getObjectManager();

/** @var Set $attributeSet */
$attributeSet = $objectManager->create(Set::class);

$entityType = $objectManager->create(\Magento\Eav\Model\Entity\Type::class)->loadByCode('catalog_product');
$defaultSetId = $objectManager->create(Product::class)->getDefaultAttributeSetid();

$attributeGroupId = $attributeSet->getDefaultGroupId($entityType->getDefaultAttributeSetId());

$attributeData = [
    'entity_type_id' => $entityType->getId(),
    'attribute_code' => 'fixed_product_attribute',
    'backend_model' => 'Magento\Weee\Model\Attribute\Backend\Weee\Tax',
    'is_required' => 0,
    'is_user_defined' => 1,
    'is_static' => 1,
    'attribute_set_id' => $defaultSetId,
    'attribute_group_id' => $attributeGroupId,
    'frontend_input' => 'weee',
    'frontend_label' => 'fixed product tax',
    'is_used_in_grid' => '1',
];

/** @var \Magento\Catalog\Model\Entity\Attribute $attribute */
$attribute = $objectManager->create(\Magento\Eav\Model\Entity\Attribute::class);
$attribute->setData($attributeData);
$attribute->save();
