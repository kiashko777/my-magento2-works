<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

/** @var Registry $registry */

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()
    ->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$categoryIDs = [444, 445, 446];
$productIDs = [444, 445];

foreach ($productIDs as $productID) {
    /** @var $product Product */
    $product = Bootstrap::getObjectManager()
        ->create(Product::class);
    $product->load($productID);
    if ($product->getId()) {
        $product->delete();
    }
}

foreach ($categoryIDs as $categoryID) {
    /** @var $category Category */
    $category = Bootstrap::getObjectManager()
        ->create(Category::class);
    $category->load($categoryID);
    if ($category->getId()) {
        $category->delete();
    }
}
