<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var Registry $registry */

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $category Category */
$category = Bootstrap::getObjectManager()->create(Category::class);
$category->load(333);
if ($category->getId()) {
    $category->delete();
}

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->load(333);
if ($product->getId()) {
    $product->delete();
}

/** @var $product Product */
$product = Bootstrap::getObjectManager()->create(Product::class);
$product->load(334);
if ($product->getId()) {
    $product->delete();
}
