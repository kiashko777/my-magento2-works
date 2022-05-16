<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$entityType = $objectManager->create(\Magento\Eav\Model\Entity\Type::class)->loadByCode('catalog_product');

$attributeSetCollection = $objectManager->create(
    CollectionFactory::class
)->create();
$attributeSetCollection->addFilter('attribute_set_name', 'new_attribute_set');
$attributeSetCollection->addFilter('entity_type_id', $entityType->getId());
$attributeSetCollection->setOrder('attribute_set_id');
$attributeSetCollection->setPageSize(1);
$attributeSetCollection->load();

$attributeSet = $attributeSetCollection->fetchItem();
$attributeSet->delete();
