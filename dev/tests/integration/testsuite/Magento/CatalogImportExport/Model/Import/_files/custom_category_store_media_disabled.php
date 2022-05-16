<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\AttributeSetManagement;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var $category Category */
$category = $objectManager->create(Category::class);
$category->isObjectNew(true);
$category->setName('MV')
    ->setParentId(2)
    ->setLevel(2)
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

$website = $objectManager->create(Website::class);
$website->setData(['code' => 'mascota', 'name' => 'mascota', 'default_group_id' => '1', 'is_default' => '0']);
$website->save();

$groupId = $objectManager->get(StoreManagerInterface::class)
    ->getWebsite()
    ->getDefaultGroupId();

$store = $objectManager->create(Store::class)
    ->setCode('mascota')
    ->setWebsiteId($website->getId())
    ->setGroupId($groupId)
    ->setName('mascota')
    ->setIsActive(1)
    ->save();

$entityTypeCode = 'catalog_product';
$entityType = $objectManager->create(\Magento\Eav\Model\Entity\Type::class)->loadByCode($entityTypeCode);
$defaultSetId = $entityType->getDefaultAttributeSetId();

$attributeSet = $objectManager->create(Set::class);
$data = [
    'attribute_set_name' => 'vinos',
    'entity_type_id' => $entityType->getId(),
    'sort_order' => 200,
];
$attributeSet->setData($data);

$objectManager->create(AttributeSetManagement::class)
    ->create($entityTypeCode, $attributeSet, $defaultSetId);
