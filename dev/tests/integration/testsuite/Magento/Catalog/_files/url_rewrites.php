<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\TestFramework\Helper\Bootstrap;

Bootstrap::getInstance()->loadArea('Adminhtml');
$objectManager = Bootstrap::getObjectManager();

/** @var $category Category */
$category1 = $objectManager->create(Category::class);
$category1->isObjectNew(true);
$category1->setName('Category 1')
    ->setParentId(2)
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();
$category1->setPath('1/2/' . $category1->getId())->save();

$product = Bootstrap::getObjectManager()->create(Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Products')
    ->setSku('simple')
    ->setPrice(10)
    ->setCategoryIds([$category1->getId()])
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->save();

$category2 = $objectManager->create(Category::class);
$category2->isObjectNew(true);
$category2->setName('Category 2')
    ->setParentId(2)
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(2)
    ->save();
$category2->setPath('1/2/' . $category2->getId())->save();

$category3 = Bootstrap::getObjectManager()->create(
    Category::class
);
$category3->isObjectNew(true);
$category3->setName('Old Root')
    ->setParentId(1)
    ->setLevel(1)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(3)
    ->save();
$category3->setPath('1/' . $category3->getId())->save();
