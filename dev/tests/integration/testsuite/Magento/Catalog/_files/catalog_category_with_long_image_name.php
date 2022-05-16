<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Category;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/catalog_category_image.php');

/** @var $category Category */
$objectManager = Bootstrap::getObjectManager();
$fileNameLong = 'magento_long_image_name_magento_long_image_name_magento_long_image_name.jpg';
$filePathLong = 'catalog/category/magento_long_image_name_magento_long_image_name_magento_long_image_name.jpg';
$categoryParent = $objectManager->create(Category::class);
$categoryParent->setName('Parent Image Category')
    ->setPath('1/2')
    ->setLevel(2)
    ->setImage($filePathLong)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

$categoryChild = $objectManager->create(Category::class);
$categoryChild->setName('Child Image Category')
    ->setPath($categoryParent->getPath())
    ->setLevel(3)
    ->setImage($fileNameLong)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(2)
    ->save();
