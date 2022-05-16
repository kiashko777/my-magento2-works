<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Model\Category;
use Magento\TestFramework\Helper\Bootstrap;

$category = Bootstrap::getObjectManager()->create(Category::class);
$category->isObjectNew(true);
$category->setId(
    3331
)->setCreatedAt(
    '2017-06-23 09:50:07'
)->setName(
    'Category with slash/ symbol'
)->setParentId(
    2
)->setPath(
    '1/2/3331'
)->setLevel(
    2
)->setAvailableSortBy(
    ['position', 'name']
)->setDefaultSortBy(
    'name'
)->setIsActive(
    true
)->setPosition(
    1
)->save();
