<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $product Product */

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$entityType = $objectManager->create(\Magento\Eav\Model\Entity\Type::class)->loadByCode('catalog_product');

// remove attribute

/** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $attributeCollection */
$attributeCollection = $objectManager->create(\Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection::class);
$attributeCollection->setFrontendInputTypeFilter('media_image');
$attributeCollection->setCodeFilter('funny_image');
$attributeCollection->setEntityTypeFilter($entityType->getId());
$attributeCollection->setPageSize(1);
$attribute = $attributeCollection->getFirstItem();
if ($attribute->getId()) {
    $attribute->delete();
}

// remove attribute set

/** @var Collection $attributeSetCollection */
$attributeSetCollection = $objectManager->create(
    Collection::class
);
$attributeSetCollection->addFilter('attribute_set_name', 'attribute_set_with_media_attribute');
$attributeSetCollection->addFilter('entity_type_id', $entityType->getId());
$attributeSetCollection->setOrder('attribute_set_id'); // descending is default value
$attributeSetCollection->setPageSize(1);

/** @var Set $attributeSet */
$attributeSet = $attributeSetCollection->getFirstItem();
if ($attributeSet->getId()) {
    $attributeSet->delete();
}
