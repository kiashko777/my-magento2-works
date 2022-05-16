<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\Set;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $category Category */
$category = $objectManager->create(Category::class);
$category->loadByAttribute('name', 'MV');
if ($category->getId()) {
    $category->delete();
}

/** @var $store Store */
$store = $objectManager->create(Store::class);
$store->load('mascota', 'code');
if ($store->getId()) {
    $store->delete();
}

/** @var $website Website */
$website = $objectManager->create(Website::class);
$website->load('mascota', 'code');
if ($website->getId()) {
    $website->delete();
}

/** @var $attributeSet Set */
$attributeSet = $objectManager->create(Set::class);
$attributeSet->load('vinos', 'attribute_set_name');
if ($attributeSet->getId()) {
    $attributeSet->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
