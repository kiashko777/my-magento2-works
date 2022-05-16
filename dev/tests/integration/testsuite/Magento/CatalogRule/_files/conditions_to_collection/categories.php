<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$collection = $objectManager->create(Collection::class);

/** @var $category Category */
$category1 = $objectManager->create(Category::class);
$category1->isObjectNew(true);
$category1
    ->setName('Category 1')
    ->setParentId(2)
    ->setPath('1/2')
    ->setLevel(2)
    ->setIsActive(true)
    ->setIsAnchor(true)
    ->setPosition(1)
    ->save();

/** @var $category Category */
$category1_1 = $objectManager->create(Category::class);
$category1_1->isObjectNew(true);
$category1_1
    ->setName('Category 1.1')
    ->setParentId($category1->getId())
    ->setPath($category1->getPath())
    ->setLevel(3)
    ->setIsActive(true)
    ->setIsAnchor(true)
    ->setPosition(1)
    ->save();

$category1_2 = $objectManager->create(Category::class);
$category1_2->isObjectNew(true);
$category1_2
    ->setName('Category 1.2')
    ->setParentId($category1->getId())
    ->setPath($category1->getPath())
    ->setLevel(3)
    ->setIsActive(true)
    ->setIsAnchor(true)
    ->setPosition(2)
    ->save();

/** @var $category Category */
$category1_1_1 = $objectManager->create(Category::class);
$category1_1_1->isObjectNew(true);
$category1_1_1
    ->setName('Category 1.1.1')
    ->setParentId($category1_1->getId())
    ->setPath($category1_1->getPath())
    ->setLevel(4)
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

/** @var $category Category */
$category2 = $objectManager->create(Category::class);
$category2->isObjectNew(true);
$category2
    ->setName('Category 2')
    ->setParentId(2)
    ->setPath('1/2')
    ->setLevel(2)
    ->setIsActive(true)
    ->setPosition(2)
    ->save();

/** @var $category Category */
$category3 = $objectManager->create(Category::class);
$category3->isObjectNew(true);
$category3
    ->setName('Category 3')
    ->setParentId(2)
    ->setPath('1/2')
    ->setLevel(2)
    ->setIsActive(true)
    ->setPosition(8)
    ->save();
