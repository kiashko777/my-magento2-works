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
/** @var $category Category */
$category = Bootstrap::getObjectManager()->create(Category::class);
$category->isObjectNew(true);
$category->setId(3)
    ->setName('Category 1')
    ->setParentId(2)
    ->setPath('1/2/3')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

$urlKeys = ['url-key', 'url-key-1', 'url-key-2', 'url-key-5', 'url-key-1000', 'url-key-999', 'url-key-asdf'];

foreach ($urlKeys as $i => $urlKey) {
    $id = $i + 1;
    $product = Bootstrap::getObjectManager()->create(
        Product::class
    );
    $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
        ->setStoreId(1)
        ->setAttributeSetId(4)
        ->setWebsiteIds([1])
        ->setName('Simple Products ' . $id)
        ->setSku('simple-' . $id)
        ->setPrice(10)
        ->setCategoryIds([3])
        ->setVisibility(Visibility::VISIBILITY_BOTH)
        ->setStatus(Status::STATUS_ENABLED)
        ->setUrlKey($urlKey)->setUrlPath($urlKey)
        ->save();
}
